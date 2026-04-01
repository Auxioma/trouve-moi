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

use App\Entity\Testimonial;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TestimonialFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer tous les users
        $users = $manager->getRepository(User::class)->findAll();

        if (\count($users) < 2) {
            return;
        }

        for ($i = 0; $i < 30; ++$i) {
            $testimonial = new Testimonial();

            $author = $users[array_rand($users)];
            $artisan = $users[array_rand($users)];

            // éviter auteur = artisan
            while ($author === $artisan) {
                $artisan = $users[array_rand($users)];
            }

            $testimonial->setAuthor($author);
            $testimonial->setArtisan($artisan);
            $testimonial->setDescription($faker->sentence(30));
            $testimonial->setReview($faker->numberBetween(0, 5));
            $testimonial->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 months', 'now')
            ));

            $manager->persist($testimonial);
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
