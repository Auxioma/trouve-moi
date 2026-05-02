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

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ){
    }
    
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($admin, 'admin'))
            ->setFirstName('Admin')
            ->setLastName('Admin')
        ;
        $manager->persist($admin);

        /* Les utilisateurs du site internet */
        $visiteur = new User();
        $visiteur->setEmail('visiteur@visiteur.visiteur')
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($visiteur, 'visiteur'))
            ->setFirstName('Visiteur')
            ->setLastName('Visiteur')
        ;
        $manager->persist($visiteur);

        /* Les artisans du site internet */
        $artisan = new User();
        $artisan->setEmail('artisan@artisan.artisan')
            ->setRoles(['ROLE_ARTISAN'])
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($artisan, 'artisan'))
            ->setFirstName('Artisan')
            ->setLastName('Artisan')
        ;
        $manager->persist($artisan);

        $manager->flush();
    }
}
