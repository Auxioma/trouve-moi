<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('admin@admin.admin');

        $user->setRoles([
            'ROLE_ARTISAN'
        ]);

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'admin'
            )
        );

        $user->setIsVerified(true);

        $user->setFirstName('Guillaume');
        $user->setLastName('Devaux');

        $user->setCompagny('Auxioma Web Agency');

        $user->setPhoneNumber('0601020304');

        $user->setSiren('123456789');

        $user->setAddress('10 rue de Paris');

        $user->setPostalCode('76600');

        $user->setCity('Le Havre');

        $user->setLatitude('49.4944');

        $user->setLongitude('0.1079');

        $manager->persist($user);

        $manager->flush();
    }
}