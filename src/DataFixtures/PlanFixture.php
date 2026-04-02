<?php

namespace App\DataFixtures;

use App\Entity\Plan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlanFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // PLAN GRATUIT
        $free = new Plan();
        $free->setName('Gratuit');
        $free->setCode('free');
        $free->setPriceMonthly('0.00');
        $free->setPriceYearly('0.00');
        $free->setIsActive(true);
        $free->setFeatures([
            '1 annonce',
            'Messagerie',
            'Profil public',
        ]);
        $manager->persist($free);

        // PLAN PRO
        $pro = new Plan();
        $pro->setName('Pro');
        $pro->setCode('pro');
        $pro->setPriceMonthly('19.90');
        $pro->setPriceYearly('199.00');
        $pro->setIsActive(true);
        $pro->setFeatures([
            'Annonces illimitées',
            'Messagerie',
            'Profil public',
            'Priorité dans les recherches',
        ]);
        $manager->persist($pro);

        // PLAN PREMIUM
        $premium = new Plan();
        $premium->setName('Premium');
        $premium->setCode('premium');
        $premium->setPriceMonthly('39.90');
        $premium->setPriceYearly('399.00');
        $premium->setIsActive(true);
        $premium->setFeatures([
            'Annonces illimitées',
            'Messagerie',
            'Profil public',
            'Priorité dans les recherches',
            'Badge premium',
            'Support prioritaire',
        ]);
        $manager->persist($premium);

        $manager->flush();
    }
}