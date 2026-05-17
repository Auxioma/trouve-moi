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
    private const REVIEWS = [
        '5',
        '5',
        '4',
        '3',
        '2',
        '1',
    ];

    private const TESTIMONIALS = [
        'Travail impeccable, artisan très professionnel et ponctuel.',
        'Très satisfait du résultat final, je recommande vivement.',
        'Communication parfaite du début à la fin du chantier.',
        'Intervention rapide et travail propre.',
        'Excellent artisan, sérieux et efficace.',
        'Très bon rapport qualité prix, rien à redire.',
        'Travaux réalisés dans les délais annoncés.',
        'Une équipe très agréable et professionnelle.',
        'Le chantier a été parfaitement exécuté.',
        'Je referai appel à cet artisan sans hésitation.',
        'Très bonne expérience du début à la fin.',
        'Artisan compétent et très réactif.',
        'Résultat au-dessus de mes attentes.',
        'Travail soigné avec beaucoup de professionnalisme.',
        'Je recommande cet artisan pour son sérieux.',
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /*
        |--------------------------------------------------------------------------
        | TÉMOIGNAGES POUR LES ARTISANS
        |--------------------------------------------------------------------------
        */
        for ($i = 1; $i <= 500; ++$i) {
            /*
            |--------------------------------------------------------------------------
            | ENTRE 1 ET 80 AVIS PAR ARTISAN
            |--------------------------------------------------------------------------
            */
            $testimonialCount = random_int(1, 80);

            for ($j = 1; $j <= $testimonialCount; ++$j) {
                $testimonial = new Testimonial();

                /**
                 * Auteur du témoignage
                 * Ici on utilise le visiteur principal.
                 */
                $author = $this->getReference(UserFixtures::USER_VISITEUR, User::class);

                /**
                 * Artisan concerné.
                 */
                $artisan = $this->getReference(
                    \sprintf('user_artisan_%03d', $i),
                    User::class
                );

                $testimonial->setAuthor($author);

                $testimonial->setArtisan($artisan);

                $testimonial->setReview(
                    self::REVIEWS[array_rand(self::REVIEWS)]
                );

                $testimonial->setDescription(
                    self::TESTIMONIALS[array_rand(self::TESTIMONIALS)]
                );

                $testimonial->setCreatedAt(
                    \DateTimeImmutable::createFromMutable(
                        $faker->dateTimeBetween('-2 years', 'now')
                    )
                );

                $manager->persist($testimonial);
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
