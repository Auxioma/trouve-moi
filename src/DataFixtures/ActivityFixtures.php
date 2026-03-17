<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActivityFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {

        $activities = [

            'Maçon',
            'Électricien',
            'Plombier',
            'Chauffagiste',
            'Peintre en bâtiment',
            'Menuisier',
            'Charpentier',
            'Couvreur',
            'Carreleur',
            'Plaquiste',

            'Serrurier',
            'Vitrier',
            'Façadier',
            'Terrassier',
            'Paysagiste',
            'Jardinier',
            'Pisciniste',
            'Architecte',
            'Maître d’œuvre',
            'Diagnostiqueur immobilier',

            'Agent immobilier',
            'Courtier en travaux',
            'Installateur de climatisation',
            'Installateur photovoltaïque',
            'Technicien fibre optique',
            'Installateur alarme',
            'Domoticien',
            'Entreprise de rénovation',
            'Entreprise de démolition',
            'Entreprise de nettoyage',

            'Nettoyage industriel',
            'Ramoneur',
            'Dératiseur',
            'Désinsectiseur',
            'Société de sécurité',
            'Antenniste',
            'Frigoriste',
            'Ascensoriste',
            'Installateur borne électrique',
            'Étancheur',

            'Tailleur de pierre',
            'Staffeur',
            'Ferronnier',
            'Métallier',
            'Soudeur',
            'Constructeur maison',
            'Constructeur bois',
            'Installateur cuisine',
            'Installateur salle de bain',
            'Entreprise multiservices'
        ];

        foreach ($activities as $activityName) {

            $activity = new Activity();

            $activity->setName($activityName);

            $slug = strtolower($this->slugger->slug($activityName));
            $activity->setSlug($slug);

            $this->setReference('user_activity', $activity);

            $manager->persist($activity);
        }

        $manager->flush();
    }
}