<?php

namespace App\Command;

use App\Entity\GooglePlace;
use App\Repository\GooglePlaceRepository;
use App\Repository\UserRepository;
use App\Service\GooglePlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:google-place:sync',
    description: 'Synchronise les Google Places des artisans',
)]
class SyncGooglePlacesCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GooglePlaceRepository $googlePlaceRepository,
        private readonly GooglePlaceService $googlePlaceService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $artisans = $this->userRepository->findAllArtisans();

        $rows = [];

        foreach ($artisans as $artisan) {
            $query = trim(sprintf(
                '%s %s',
                $artisan->getCompagny() ?? '',
                $artisan->getCity() ?? ''
            ));

            if ($query === '') {
                $rows[] = [
                    $artisan->getEmail(),
                    'Ignoré',
                    'Entreprise/ville introuvable',
                ];
                continue;
            }

            $googlePlace = $this->googlePlaceRepository->findOneByUser($artisan);

            if (
                $googlePlace !== null &&
                $googlePlace->getLastSyncAt() !== null &&
                $googlePlace->getLastSyncAt() > new \DateTimeImmutable('-12 hours')
            ) {
                $rows[] = [
                    $artisan->getEmail(),
                    'Skip',
                    'Synchronisé récemment',
                ];
                continue;
            }

            if (!$googlePlace) {
                $googlePlace = new GooglePlace();
                $googlePlace->setUser($artisan);
            }

            if (!$googlePlace->getPlaceId()) {
                $place = $this->googlePlaceService->findPlaceByText($query);

                if (!$place || empty($place['place_id'])) {
                    $rows[] = [
                        $artisan->getEmail(),
                        'Erreur',
                        sprintf('Aucun place_id trouvé pour "%s"', $query),
                    ];
                    continue;
                }

                $googlePlace->setPlaceId($place['place_id']);
            }

            $details = $this->googlePlaceService->getPlaceDetails($googlePlace->getPlaceId());

            if (!$details) {
                $rows[] = [
                    $artisan->getEmail(),
                    'Erreur',
                    'Détails Google introuvables',
                ];
                continue;
            }

            $googlePlace
                ->setDisplayName($details['display_name'])
                ->setRating($details['rating'])
                ->setReviews($details['reviews'])
                ->setReviewsCount($details['reviews_count'])
                ->setGoogleMapsUri($details['google_maps_uri'])
                ->setLastSyncAt(new \DateTimeImmutable())
            ;

            $this->entityManager->persist($googlePlace);

            $rows[] = [
                $artisan->getEmail(),
                'OK',
                $googlePlace->getPlaceId(),
            ];
        }

        $this->entityManager->flush();

        $table = new Table($output);
        $table->setHeaders(['Artisan', 'Statut', 'Message']);
        $table->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
