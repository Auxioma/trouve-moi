<?php

namespace App\DataFixtures;

use App\Entity\Testimonial;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class TestimonialFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer tous les users
        $users = $manager->getRepository(User::class)->findAll();

        if (count($users) < 2) {
            return;
        }

        for ($i = 0; $i < 30; $i++) {
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