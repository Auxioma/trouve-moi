<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_ADMIN = 'user_admin';
    public const USER_VISITEUR = 'user_visiteur';
    public const USER_ARTISAN = 'user_artisan';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');

        $manager->persist($admin);
        $this->addReference(self::USER_ADMIN, $admin);

        $visiteur = new User();
        $visiteur->setEmail('visiteur@visiteur.visiteur');
        $visiteur->setRoles(['ROLE_USER']);
        $visiteur->setIsVerified(true);
        $visiteur->setPassword($this->passwordHasher->hashPassword($visiteur, 'visiteur'));
        $visiteur->setFirstName('Visiteur');
        $visiteur->setLastName('Visiteur');

        $manager->persist($visiteur);
        $this->addReference(self::USER_VISITEUR, $visiteur);

        $artisan = new User();
        $artisan->setEmail('artisan@artisan.artisan');
        $artisan->setRoles(['ROLE_ARTISAN']);
        $artisan->setIsVerified(true);
        $artisan->setPassword($this->passwordHasher->hashPassword($artisan, 'artisan'));
        $artisan->setFirstName('Artisan');
        $artisan->setLastName('Test');
        $artisan->setCompagny('Artisan SARL');
        $artisan->setPhoneNumber('0123456789');
        $artisan->setSiren('123456789');
        $artisan->setAddress('123 Rue de l\'Artisanat');
        $artisan->setPostalCode('75000');
        $artisan->setCity('Paris');
        $artisan->setLatitude(48.8566);
        $artisan->setLongitude(2.3522);
        $artisan->setDescription('Entreprise artisanale spécialisée dans la rénovation.');
        $artisan->setGrandeDescription('Entreprise artisanale spécialisée dans la rénovation complète.');
        $artisan->setWebsite('https://www.artisan-sarl.fr');
        $artisan->setImageName('artisan.png');

        $artisan->setActivity(
            $this->getReference(ActivityFixtures::ACTIVITY_MACON, Activity::class)
        );

        $manager->persist($artisan);
        $this->addReference(self::USER_ARTISAN, $artisan);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixtures::class,
        ];
    }
}
