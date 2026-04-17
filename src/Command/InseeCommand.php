<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-plumbers',
    description: 'Import des plombiers autour de Forges-les-Eaux avec anti-doublon et gestion du rate limit',
)]
class InseeCommand extends Command
{
    private const API_URL = 'https://recherche-entreprises.api.gouv.fr/near_point';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $repo,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Centre : Forges-les-Eaux
        $centerLat = 49.6133;
        $centerLong = 1.5456;

        // Rayon final souhaité
        $radius = 100;

        // Code NAF plomberie, chauffage et conditionnement d'air
        $naf = '43.22A';

        // Rayon par appel API
        $apiRadius = 50;

        // Tempo de sécurité entre appels API
        $baseSleepMs = 500;

        $io->title('Import des plombiers autour de Forges-les-Eaux');

        $points = $this->generateSearchPoints($centerLat, $centerLong, $apiRadius);

        $totalInserted = 0;
        $totalSkippedDb = 0;
        $totalSkippedRun = 0;
        $totalOutOfRadius = 0;
        $totalWithoutSiret = 0;
        $totalWithoutCoords = 0;

        // Anti-doublon pendant le même import
        $seenSirets = [];

        foreach ($points as $i => $point) {
            $io->section(sprintf(
                'Zone #%d (lat: %s, long: %s)',
                $i + 1,
                $point['lat'],
                $point['long']
            ));

            $page = 1;
            $stopZone = false;

            do {
                try {
                    [$data, $headers] = $this->requestWithRateLimit($point['lat'], $point['long'], $apiRadius, $naf, $page, $io);
                } catch (\Throwable $e) {
                    $io->error('Erreur API : ' . $e->getMessage());
                    break;
                }

                if (empty($data['results'])) {
                    break;
                }

                foreach ($data['results'] as $company) {
                    $coords = $this->extractCoordinates($company);

                    if (!$coords) {
                        $totalWithoutCoords++;
                        continue;
                    }

                    $distance = $this->distance(
                        $centerLat,
                        $centerLong,
                        $coords['lat'],
                        $coords['long']
                    );

                    if ($distance > $radius) {
                        $totalOutOfRadius++;
                        continue;
                    }

                    $siret = $company['siege']['siret']
                        ?? $company['matching_etablissements'][0]['siret']
                        ?? null;

                    if (!$siret) {
                        $totalWithoutSiret++;
                        continue;
                    }

                    // Anti-doublon dans le même run
                    if (isset($seenSirets[$siret])) {
                        $totalSkippedRun++;
                        continue;
                    }

                    // Anti-doublon DB
                    $existing = $this->repo->findOneBy(['siret' => $siret]);
                    if ($existing) {
                        $seenSirets[$siret] = true;
                        $totalSkippedDb++;
                        continue;
                    }

                    $entity = new User();

                    $email = sprintf('%s@import.local', uniqid('user_', true));
                    $plainPassword = 'TempPassword!123';

                    $entity
                        ->setRoles(['ROLE_ATTENTE'])
                        ->setEmail($email)
                        ->setPassword($this->passwordHasher->hashPassword($entity, $plainPassword))
                        ->setIsVerified(false)
                        ->setFirstName($company['nom_complet'] ?? 'N/A')
                        ->setLastName($company['nom_complet'] ?? 'N/A')
                        ->setCompagny($company['nom_raison_sociale'] ?? 'N/A')
                        ->setSiren($company['siren'] ?? null)
                        ->setAddress($company['siege']['adresse'] ?? null)
                        ->setPostalCode($company['siege']['code_postal'] ?? null)
                        ->setCity($company['siege']['libelle_commune'] ?? null)
                        ->setLatitude($coords['lat'])
                        ->setLongitude($coords['long'])
                        ->setSiret($siret);

                    $this->em->persist($entity);
                    $seenSirets[$siret] = true;
                    $totalInserted++;

                    if ($totalInserted % 50 === 0) {
                        $this->em->flush();
                        $this->em->clear();
                        $io->text("Flush batch : {$totalInserted} insertions");
                    }
                }

                $totalPages = $data['total_pages'] ?? 1;
                $page++;

                // Tempo de sécurité entre appels
                usleep($baseSleepMs * 1000);

                // Si on est proche de la limite, on ralentit encore
                $remaining = isset($headers['ratelimit-remaining'][0]) ? (int) $headers['ratelimit-remaining'][0] : null;
                if ($remaining !== null && $remaining < 5) {
                    $io->warning("Rate limit presque atteint (remaining: {$remaining}), pause de sécurité...");
                    sleep(5);
                }

                if ($stopZone) {
                    break;
                }
            } while ($page <= $totalPages);
        }

        $this->em->flush();

        $io->success([
            'Import terminé',
            "Ajoutés : {$totalInserted}",
            "Ignorés (déjà en base) : {$totalSkippedDb}",
            "Ignorés (déjà vus pendant ce run) : {$totalSkippedRun}",
            "Hors rayon 100 km : {$totalOutOfRadius}",
            "Sans coordonnées : {$totalWithoutCoords}",
            "Sans SIRET : {$totalWithoutSiret}",
        ]);

        return Command::SUCCESS;
    }

    /**
     * Requête API avec gestion du 429.
     */
    private function requestWithRateLimit(
        float $lat,
        float $long,
        int $radius,
        string $naf,
        int $page,
        SymfonyStyle $io,
        int $maxRetries = 5
    ): array {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                $response = $this->httpClient->request('GET', self::API_URL, [
                    'query' => [
                        'lat' => $lat,
                        'long' => $long,
                        'radius' => $radius,
                        'activite_principale' => $naf,
                        'per_page' => 25,
                        'page' => $page,
                    ],
                    'timeout' => 10,
                ]);

                $statusCode = $response->getStatusCode();
                $headers = $response->getHeaders(false);

                if ($statusCode === 429) {
                    $retryAfter = isset($headers['retry-after'][0]) ? (int) $headers['retry-after'][0] : 10;
                    $io->warning("429 reçu. Pause de {$retryAfter}s avant retry (tentative {$attempt}/{$maxRetries}).");
                    sleep($retryAfter);
                    continue;
                }

                if ($statusCode !== 200) {
                    throw new \RuntimeException("Erreur API HTTP {$statusCode}");
                }

                $data = $response->toArray(false);

                return [$data, $headers];
            } catch (TransportExceptionInterface $e) {
                $wait = min(2 ** $attempt, 30);
                $io->warning("Erreur réseau : {$e->getMessage()} - retry dans {$wait}s");
                sleep($wait);
            }
        }

        throw new \RuntimeException('Impossible d’interroger l’API après plusieurs tentatives.');
    }

    private function generateSearchPoints(float $lat, float $lng, float $distance): array
    {
        $points = [];
        $points[] = ['lat' => $lat, 'long' => $lng];

        foreach ([0, 90, 180, 270, 45, 135, 225, 315] as $bearing) {
            $points[] = $this->move($lat, $lng, $distance, $bearing);
        }

        return $points;
    }

    private function move(float $lat, float $lng, float $distance, float $bearing): array
    {
        $R = 6371;
        $bearing = deg2rad($bearing);

        $lat1 = deg2rad($lat);
        $lon1 = deg2rad($lng);

        $lat2 = asin(
            sin($lat1) * cos($distance / $R) +
            cos($lat1) * sin($distance / $R) * cos($bearing)
        );

        $lon2 = $lon1 + atan2(
            sin($bearing) * sin($distance / $R) * cos($lat1),
            cos($distance / $R) - sin($lat1) * sin($lat2)
        );

        return [
            'lat' => rad2deg($lat2),
            'long' => rad2deg($lon2),
        ];
    }

    private function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function extractCoordinates(array $company): ?array
    {
        if (isset($company['siege']['latitude'], $company['siege']['longitude'])) {
            return [
                'lat' => (float) $company['siege']['latitude'],
                'long' => (float) $company['siege']['longitude'],
            ];
        }

        if (!empty($company['matching_etablissements'])) {
            foreach ($company['matching_etablissements'] as $e) {
                if (isset($e['latitude'], $e['longitude'])) {
                    return [
                        'lat' => (float) $e['latitude'],
                        'long' => (float) $e['longitude'],
                    ];
                }
            }
        }

        return null;
    }
}