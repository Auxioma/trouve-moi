<?php

/**
 * Copyright (c) 2026 Auxioma Web Agency
 * https://trouvemoi.eu
 *
 * Ce fichier fait partie du projet Trouvemoi.eu développé par Auxioma Web Agency.
 * Tous droits réservés.
 *
 * Ce code source, son architecture, sa structure, ses scripts et ses composants
 * sont la propriété exclusive de Auxioma Web Agency et de ses partenaires.
 *
 * Toute reproduction, modification, distribution, publication ou utilisation,
 * totale ou partielle, sans autorisation écrite préalable est strictement interdite.
 *
 * Ce code est confidentiel et propriétaire.
 * Droit applicable : Monde.
 */

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Enum\UserProfileStatus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_ADMIN = 'user_admin';
    public const USER_VISITEUR = 'user_visiteur';
    public const USER_ARTISAN = 'user_artisan';

    private const BATCH_SIZE = 50;

    private const STATUSES = [
        UserProfileStatus::PARTIAL,
        UserProfileStatus::VALIDATED,
        UserProfileStatus::BANNED,
    ];

    private const ACTIVITY_REFERENCES = [
        ActivityFixtures::ACTIVITY_MACON,
        ActivityFixtures::ACTIVITY_ELECTRICIEN,
        ActivityFixtures::ACTIVITY_PLOMBIER,
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        $admin = new User();
        $admin->setEmail('admin@admin.admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setProfileStatus(UserProfileStatus::VALIDATED);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');

        $manager->persist($admin);
        $this->addReference(self::USER_ADMIN, $admin);

        /*
        |--------------------------------------------------------------------------
        | VISITEUR
        |--------------------------------------------------------------------------
        */
        $visiteur = new User();
        $visiteur->setEmail('visiteur@visiteur.visiteur');
        $visiteur->setRoles(['ROLE_USER']);
        $visiteur->setIsVerified(true);
        $visiteur->setProfileStatus(UserProfileStatus::VALIDATED);
        $visiteur->setPassword($this->passwordHasher->hashPassword($visiteur, 'visiteur'));
        $visiteur->setFirstName('Visiteur');
        $visiteur->setLastName('Visiteur');

        $manager->persist($visiteur);
        $this->addReference(self::USER_VISITEUR, $visiteur);

        $manager->flush();

        /*
        |--------------------------------------------------------------------------
        | 500 ARTISANS
        |--------------------------------------------------------------------------
        */
        for ($i = 1; $i <= 500; ++$i) {
            $artisan = new User();

            $artisan->setEmail(\sprintf('artisan%03d@artisan.fr', $i));
            $artisan->setRoles(['ROLE_ARTISAN']);
            $artisan->setIsVerified($faker->boolean(90));
            $artisan->setProfileStatus(self::STATUSES[array_rand(self::STATUSES)]);
            $artisan->setPassword($this->passwordHasher->hashPassword($artisan, 'artisan'));
            $artisan->setFirstName($faker->firstName());
            $artisan->setLastName($faker->lastName());
            $artisan->setCompagny($faker->company());
            $artisan->setPhoneNumber('+33'.$faker->numerify('6########'));
            $artisan->setSiren($faker->numerify('#########'));
            $artisan->setAddress($faker->streetAddress());
            $artisan->setPostalCode($faker->postcode());
            $artisan->setCity($faker->city());
            $artisan->setLatitude((float) $faker->latitude(41, 51));
            $artisan->setLongitude((float) $faker->longitude(-5, 9));
            $artisan->setDescription($faker->sentence(12));
            $artisan->setGrandeDescription($faker->paragraphs(3, true));
            $artisan->setWebsite('https://www.'.$faker->slug().'.fr');
            $artisan->setImageName('artisan-default.png');

            $randomActivityReference = self::ACTIVITY_REFERENCES[array_rand(self::ACTIVITY_REFERENCES)];
            $artisan->setActivity($this->getReference($randomActivityReference, Activity::class));

            $manager->persist($artisan);

            $this->addReference(\sprintf('user_artisan_%03d', $i), $artisan);

            if (1 === $i) {
                $this->addReference(self::USER_ARTISAN, $artisan);
            }

            /*
            |--------------------------------------------------------------------------
            | FLUSH PAR BATCH — évite la saturation mémoire sur 500 entités
            |--------------------------------------------------------------------------
            */
            if (0 === ($i % self::BATCH_SIZE)) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixtures::class,
        ];
    }
}
