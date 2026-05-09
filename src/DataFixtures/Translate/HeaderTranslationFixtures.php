<?php

namespace App\DataFixtures\Translate;

use App\Entity\Translation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class HeaderTranslationFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['header_translation'];
    }

    public function load(ObjectManager $manager): void
    {
        $translations = [

            // MENU
            [
                'key' => 'header.menu.home',
                'translation' => 'Accueil',
            ],
            [
                'key' => 'header.menu.features',
                'translation' => 'Features',
            ],
            [
                'key' => 'header.menu.pricing',
                'translation' => 'Pricing',
            ],
            [
                'key' => 'header.menu.disabled',
                'translation' => 'Disabled',
            ],

            // AUTH
            [
                'key' => 'header.auth.login',
                'translation' => 'Connexion',
            ],
            [
                'key' => 'header.auth.start',
                'translation' => 'Commencer',
            ],

            // HERO
            [
                'key' => 'header.hero.title',
                'translation' => 'L\'excellence artisanale',
            ],
            [
                'key' => 'header.hero.title_span',
                'translation' => 'à votre portée',
            ],
            [
                'key' => 'header.hero.description',
                'translation' => 'Trouvez les meilleurs experts certifiés pour vos projets d\'architecture et de rénovation. Une sélection rigoureuse pour une tranquillité d\'esprit totale.',
            ],

            // SEARCH
            [
                'key' => 'header.search.activity_placeholder',
                'translation' => 'Quel artisan cherchez-vous ?',
            ],
            [
                'key' => 'header.search.location_placeholder',
                'translation' => 'Localisation',
            ],
            [
                'key' => 'header.search.button',
                'translation' => 'Rechercher',
            ],

            // STATS
            [
                'key' => 'header.stats.artisans',
                'translation' => '1,200+ artisans vérifiés ce mois-ci',
            ],
        ];

        foreach ($translations as $item) {

            $existingTranslation = $manager
                ->getRepository(Translation::class)
                ->findOneBy([
                    'translationKey' => $item['key'],
                    'locale' => 'fr',
                ]);

            if ($existingTranslation) {
                continue;
            }

            $translation = new Translation();

            $translation
                ->setTranslationKey($item['key'])
                ->setLocale('fr')
                ->setTranslation($item['translation'])
                ->setPage('header')
            ;

            $manager->persist($translation);
        }

        $manager->flush();
    }
}