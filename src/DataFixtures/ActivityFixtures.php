<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Services;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActivityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->getActivitiesWithServices();

        foreach ($data as $activityName => $services) {
            $activity = new Activity();
            $activity->setName($activityName);
            $activity->setSlug($this->slugify($activityName));

            $manager->persist($activity);

            foreach ($services as $serviceName) {
                $service = new Services();
                $service->setName($serviceName);
                $service->setActivity($activity);

                $manager->persist($service);
            }
        }

        $manager->flush();
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/iu', '-', $text);

        return trim((string) $text, '-');
    }

    private function getActivitiesWithServices(): array
    {
        return [
            'Plomberie' => [
                'Installation sanitaire',
                'Débouchage canalisation',
                'Réparation fuite',
                'Remplacement chauffe-eau',
            ],
            'Électricité' => [
                'Installation électrique',
                'Mise aux normes',
                'Dépannage électrique',
                'Pose de tableau électrique',
            ],
            'Maçonnerie' => [
                'Construction mur',
                'Rénovation façade',
                'Dalle béton',
                'Ouverture de mur porteur',
            ],
            'Peinture' => [
                'Peinture intérieure',
                'Peinture extérieure',
                'Ravalement de façade',
                'Pose d’enduit',
            ],
            'Menuiserie' => [
                'Pose de fenêtres',
                'Pose de portes',
                'Fabrication sur mesure',
                'Aménagement intérieur',
            ],
            'Carrelage' => [
                'Pose de carrelage',
                'Pose de faïence',
                'Rénovation salle de bain',
                'Terrasse carrelée',
            ],
            'Chauffage' => [
                'Installation chaudière',
                'Entretien chaudière',
                'Pose de pompe à chaleur',
                'Dépannage chauffage',
            ],
            'Couverture' => [
                'Réparation toiture',
                'Pose de tuiles',
                'Nettoyage toiture',
                'Isolation toiture',
            ],
        ];
    }
}