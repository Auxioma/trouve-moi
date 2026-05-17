<?php

namespace App\DataFixtures;

use App\Entity\Devis;
use App\Entity\DevisArtisan;
use App\Entity\Enum\DebutChantierEnum;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DevisFixtures extends Fixture implements DependentFixtureInterface
{
    private const MIN_DEVIS = 1;
    private const MAX_DEVIS = 15;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var EntityManagerInterface $manager */
        $userRepository = $manager->getRepository(User::class);

        /** @var User[] $visiteurs */
        $visiteurs = $userRepository->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_USER%')
            ->getQuery()
            ->getResult();

        /** @var User[] $artisans */
        $artisans = $userRepository->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ARTISAN%')
            ->getQuery()
            ->getResult();

        if ([] === $visiteurs || [] === $artisans) {
            return;
        }

        $allDevis = [];

        /*
        |--------------------------------------------------------------------------
        | 1. Chaque visiteur crée entre 1 et 15 devis
        |--------------------------------------------------------------------------
        */
        foreach ($visiteurs as $visiteur) {
            $numberOfDevis = random_int(self::MIN_DEVIS, self::MAX_DEVIS);

            for ($i = 1; $i <= $numberOfDevis; ++$i) {
                $artisanRandom = $artisans[array_rand($artisans)];

                $devis = new Devis();
                $devis->setVisiteur($visiteur);
                $devis->setTitre($faker->randomElement([
                    'Rénovation complète appartement',
                    'Travaux de plomberie',
                    'Installation électrique',
                    'Rénovation salle de bain',
                    'Création cuisine équipée',
                    'Peinture intérieure',
                    'Travaux de maçonnerie',
                    'Pose de carrelage',
                    'Isolation thermique',
                    'Réparation urgente',
                ]));
                $devis->setDescription($faker->paragraphs(3, true));
                $devis->setSurface((string) $faker->numberBetween(10, 250));
                $devis->setBudget((string) $faker->numberBetween(500, 75000));
                $devis->setCreatedAt(\DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-8 months', 'now')
                ));
                $devis->setUpdatedAt(null);
                $devis->setDebutChantier($faker->randomElement(DebutChantierEnum::cases()));

                if (null !== $artisanRandom->getActivity()) {
                    $devis->setMetier($artisanRandom->getActivity());
                }

                $manager->persist($devis);

                $allDevis[] = $devis;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Chaque artisan reçoit entre 1 et 15 demandes de devis
        |--------------------------------------------------------------------------
        */
        foreach ($artisans as $artisan) {
            $numberOfRequests = random_int(self::MIN_DEVIS, self::MAX_DEVIS);

            for ($i = 1; $i <= $numberOfRequests; ++$i) {
                $devis = $allDevis[array_rand($allDevis)];

                $sendAt = \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-6 months', 'now')
                );

                $devisArtisan = new DevisArtisan();
                $devisArtisan->setDevis($devis);
                $devisArtisan->setArtisan($artisan);
                $devisArtisan->setStatus($faker->randomElement([
                    'envoye',
                    'vu',
                    'accepte',
                    'refuse',
                    'repondu',
                ]));
                $devisArtisan->setSendAt($sendAt);

                // Obligatoire dans ton entité actuelle
                $devisArtisan->setViewAt($sendAt->modify('+'.random_int(1, 72).' hours'));
                $devisArtisan->setAnswerAt($sendAt->modify('+'.random_int(2, 120).' hours'));

                $devisArtisan->setMessage($faker->optional(70)->paragraph());

                $manager->persist($devisArtisan);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
