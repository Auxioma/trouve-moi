<?php

namespace App\DataFixtures\Translate;

use App\Entity\Translation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class HomeTranslationFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['home_translation'];
    }

    public function load(ObjectManager $manager): void
    {
        $translations = [

            // PROCESS
            [
                'key' => 'home.process.title',
                'translation' => 'PROCESSUS SIMPLIFIÉ',
            ],
            [
                'key' => 'home.process.subtitle',
                'translation' => '3 étapes pour trouver votre expert',
            ],
            [
                'key' => 'home.process.step_realisation.title',
                'translation' => 'Réalisation',
            ],
            [
                'key' => 'home.process.step_realisation.text',
                'translation' => 'Travaillez avec un pro sereinement, protégé par nos garanties et notre suivi personnalisé.',
            ],
            [
                'key' => 'home.process.step_search.title',
                'translation' => 'Recherchez',
            ],
            [
                'key' => 'home.process.step_search.text',
                'translation' => 'Utilisez notre moteur intelligent pour cibler les artisans spécialisés selon vos besoins précis.',
            ],
            [
                'key' => 'home.process.step_quote.title',
                'translation' => 'Comparez les Devis',
            ],
            [
                'key' => 'home.process.step_quote.text',
                'translation' => 'Recevez des propositions détaillées et transparentes en moins de 48 heures pour votre projet.',
            ],

            // SATISFACTION
            [
                'key' => 'home.satisfaction.client',
                'translation' => 'Satisfaction Client',
            ],
            [
                'key' => 'home.satisfaction.insurance_title',
                'translation' => 'Assurance Décennale',
            ],
            [
                'key' => 'home.satisfaction.insurance_text',
                'translation' => 'Tous nos partenaires sont couverts.',
            ],

            // WHY
            [
                'key' => 'home.why.title',
                'translation' => 'POURQUOI NOUS CHOISIR ?',
            ],
            [
                'key' => 'home.why.subtitle',
                'translation' => 'Plus qu\'un simple annuaire, un gage de confiance',
            ],
            [
                'key' => 'home.why.diploma_title',
                'translation' => 'Vérification des diplômes',
            ],
            [
                'key' => 'home.why.diploma_text',
                'translation' => 'Chaque artisan passe un entretien rigoureux et voit ses diplômes authentifiés par nos experts.',
            ],
            [
                'key' => 'home.why.insurance_title',
                'translation' => 'Assurance décennale à jour',
            ],
            [
                'key' => 'home.why.insurance_text',
                'translation' => 'Nous vérifions mensuellement la validité des assurances professionnelles de nos membres.',
            ],
            [
                'key' => 'home.why.follow_title',
                'translation' => 'Suivi personnalisé',
            ],
            [
                'key' => 'home.why.follow_text',
                'translation' => 'Un conseiller dédié vous accompagne de la signature du devis à la réception de chantier.',
            ],

            // SELECTION
            [
                'key' => 'home.selection.title',
                'translation' => 'NOTRE SÉLECTION',
            ],
            [
                'key' => 'home.selection.subtitle',
                'translation' => 'Artisans de confiance',
            ],
            [
                'key' => 'home.selection.default_activity',
                'translation' => 'Default',
            ],
            [
                'key' => 'home.selection.default_artisan',
                'translation' => 'Artisan',
            ],
            [
                'key' => 'home.selection.badge',
                'translation' => 'QUALIBAT RGE',
            ],

            // EXPERIENCES
            [
                'key' => 'home.experiences.title',
                'translation' => 'EXPÉRIENCES',
            ],
            [
                'key' => 'home.experiences.subtitle',
                'translation' => 'Ce qu\'en disent nos clients',
            ],
            [
                'key' => 'home.experiences.default_client',
                'translation' => 'Client',
            ],

            // FAQ
            [
                'key' => 'home.faq.title',
                'translation' => 'Questions Fréquentes',
            ],
            [
                'key' => 'home.faq.question_1',
                'translation' => 'Comment vérifiez-vous les artisans ?',
            ],
            [
                'key' => 'home.faq.answer_1',
                'translation' => 'Chaque artisan est soigneusement vérifié : identité, qualifications, assurances et avis clients. Nous sélectionnons uniquement des professionnels fiables pour garantir la qualité des prestations.',
            ],
            [
                'key' => 'home.faq.question_2',
                'translation' => 'Y a-t-il des frais de mise en relation ?',
            ],
            [
                'key' => 'home.faq.answer_2',
                'translation' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            ],
            [
                'key' => 'home.faq.question_3',
                'translation' => 'Que couvre l’assurance décennale ?',
            ],
            [
                'key' => 'home.faq.answer_3',
                'translation' => 'L’assurance décennale couvre les dommages pouvant affecter la solidité de l’ouvrage ou le rendre impropre à son usage pendant 10 ans après réception des travaux.',
            ],

            // BLOG
            [
                'key' => 'home.blog.title',
                'translation' => 'ACTUALITÉS',
            ],
            [
                'key' => 'home.blog.subtitle',
                'translation' => 'Le Journal de l\'Artisanat',
            ],
            [
                'key' => 'home.blog.category',
                'translation' => 'ARCHITECTURE',
            ],
            [
                'key' => 'home.blog.read_time',
                'translation' => '5 MIN READ',
            ],
            [
                'key' => 'home.blog.article_title',
                'translation' => 'Les tendances architecturales qui vont dominer 2025',
            ],
            [
                'key' => 'home.blog.article_text',
                'translation' => 'Découvrez comment les nouveaux matériaux durables transforment le paysage urbain…',
            ],
            [
                'key' => 'home.blog.read_more',
                'translation' => 'lire la suite',
            ],

            // PROVIDER
            [
                'key' => 'home.provider.title',
                'translation' => 'Devenez prestataire sur trouvemoi',
            ],
            [
                'key' => 'home.provider.text',
                'translation' => 'Inscrivez-vous et trouvez des nouveaux clients, développez votre notoriété et commencez à optimisez vos revenus dès maintenant.',
            ],
            [
                'key' => 'home.provider.button',
                'translation' => 'Commencer',
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
                ->setPage('home')
            ;

            $manager->persist($translation);
        }

        $manager->flush();
    }
}