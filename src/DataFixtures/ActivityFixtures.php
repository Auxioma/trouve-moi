<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActivityFixtures extends Fixture
{
    public const ACTIVITY_MACON = 'activity_macon';
    public const ACTIVITY_ELECTRICIEN = 'activity_electricien';
    public const ACTIVITY_PLOMBIER = 'activity_plombier';

    public function __construct(
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $activities = [
            self::ACTIVITY_MACON => ['name' => 'Maçon', 'naf' => '43.99C'],
            self::ACTIVITY_ELECTRICIEN => ['name' => 'Électricien', 'naf' => '43.21A'],
            self::ACTIVITY_PLOMBIER => ['name' => 'Plombier', 'naf' => '43.22A'],
        ];

        foreach ($activities as $reference => $data) {
            $activity = new Activity();
            $activity->setName($data['name']);
            $activity->setNaf($data['naf']);
            $activity->setSlug($this->slugger->slug($data['name'])->lower()->toString());

            $manager->persist($activity);
            $this->addReference($reference, $activity);
        }

        $manager->flush();
    }
}
