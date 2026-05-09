<?php

namespace App\DataFixtures;

use App\Entity\Pictures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PicturesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $artisan */
        $artisan = $this->getReference(UserFixtures::USER_ARTISAN, User::class);

        $pictures = [
            'artisan-01.jpg',
            'artisan-02.jpg',
            'artisan-03.jpg',
            'artisan-04.jpg',
            'artisan-05.jpg',
        ];

        foreach ($pictures as $imageName) {
            $picture = new Pictures();
            $picture->setName($imageName);
            $picture->setUser($artisan);
            $picture->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($picture);
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
