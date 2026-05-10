<?php

namespace App\DataFixtures;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Repository\BlogCategoryRepository;
use App\Repository\BlogPostRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

final class BlogFixtures extends Fixture
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly BlogCategoryRepository $blogCategoryRepository,
        private readonly BlogPostRepository $blogPostRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        $categories = [
            'Conseils travaux',
            'Rénovation',
            'Électricité',
            'Plomberie',
            'Isolation',
            'Décoration',
            'Sécurité',
            'Guide artisan',
        ];

        $categoryEntities = [];

        foreach ($categories as $categoryName) {
            $slug = $this->createSlug($categoryName);

            $category = $this->blogCategoryRepository->findOneBy([
                'slug' => $slug,
            ]);

            if (!$category) {
                $category = new BlogCategory();
                $category->setName($categoryName);
                $category->setSlug($slug);
                $category->setCreatedAt($now);

                $manager->persist($category);
            }

            $categoryEntities[$slug] = $category;
        }

        $posts = [
            [
                'title' => 'Comment bien choisir un artisan pour vos travaux ?',
                'category' => 'Guide artisan',
                'isPublished' => true,
                'publishedAt' => '-10 days',
                'content' => <<<TEXT
Choisir un artisan ne doit jamais se faire uniquement sur le prix.

Avant de confier vos travaux, vérifiez son expérience, ses réalisations, ses assurances professionnelles et sa capacité à expliquer clairement son intervention.

Un bon artisan prend le temps d’écouter votre besoin, de vous conseiller et de vous remettre un devis clair. Il doit aussi être capable de vous informer sur les délais, les matériaux utilisés et les garanties proposées.

Sur une plateforme comme TrouveMoi, l’objectif est de faciliter cette mise en relation entre particuliers et professionnels qualifiés.
TEXT,
            ],
            [
                'title' => 'Les erreurs à éviter avant de lancer une rénovation',
                'category' => 'Rénovation',
                'isPublished' => true,
                'publishedAt' => '-8 days',
                'content' => <<<TEXT
Une rénovation réussie commence toujours par une bonne préparation.

L’une des erreurs les plus fréquentes consiste à démarrer les travaux sans budget précis. Il est important de prévoir une marge pour les imprévus, surtout dans les logements anciens.

Il faut aussi éviter de choisir les artisans dans la précipitation. Prenez le temps de comparer plusieurs devis, de vérifier les délais et de bien comprendre chaque prestation.

Enfin, pensez à organiser les travaux dans le bon ordre : démolition, gros œuvre, réseaux techniques, isolation, finitions et décoration.
TEXT,
            ],
            [
                'title' => 'Quand faut-il refaire son installation électrique ?',
                'category' => 'Électricité',
                'isPublished' => true,
                'publishedAt' => '-6 days',
                'content' => <<<TEXT
Une installation électrique ancienne peut représenter un danger réel pour les occupants d’un logement.

Certains signes doivent vous alerter : prises abîmées, disjonctions fréquentes, tableau électrique vieillissant, absence de prise de terre ou odeur de chaud.

Faire intervenir un électricien permet de vérifier la conformité de l’installation et d’identifier les travaux nécessaires.

Une mise aux normes améliore la sécurité, mais aussi le confort quotidien, notamment si vous utilisez beaucoup d’appareils électriques.
TEXT,
            ],
            [
                'title' => 'Pourquoi faire appel à un plombier professionnel ?',
                'category' => 'Plomberie',
                'isPublished' => true,
                'publishedAt' => '-5 days',
                'content' => <<<TEXT
Une fuite d’eau ou une canalisation bouchée peut rapidement devenir un vrai problème.

Un plombier professionnel dispose des outils adaptés pour détecter l’origine de la panne et intervenir efficacement. Il peut aussi vous conseiller sur l’entretien de vos équipements sanitaires.

Faire appel à un professionnel permet d’éviter les réparations approximatives qui peuvent coûter plus cher à long terme.

Pour les travaux importants, comme la création d’une salle de bain ou le remplacement d’un chauffe-eau, son expertise est indispensable.
TEXT,
            ],
            [
                'title' => 'Isolation : un investissement rentable pour votre logement',
                'category' => 'Isolation',
                'isPublished' => true,
                'publishedAt' => '-4 days',
                'content' => <<<TEXT
Une bonne isolation permet de réduire les pertes de chaleur et d’améliorer le confort intérieur.

Les zones les plus importantes à traiter sont généralement les combles, les murs, les fenêtres et les planchers bas.

Même si les travaux représentent un budget, ils peuvent permettre de réaliser des économies importantes sur les factures d’énergie.

Un artisan qualifié pourra vous aider à choisir les matériaux adaptés à votre logement et à votre budget.
TEXT,
            ],
            [
                'title' => 'Comment moderniser son intérieur sans tout refaire ?',
                'category' => 'Décoration',
                'isPublished' => true,
                'publishedAt' => '-3 days',
                'content' => <<<TEXT
Il n’est pas toujours nécessaire de tout casser pour donner un nouveau style à son intérieur.

Changer les couleurs, améliorer l’éclairage, remplacer certains meubles ou ajouter des matériaux plus chaleureux peut transformer une pièce.

Un peintre, un décorateur ou un menuisier peut vous aider à obtenir un résultat plus harmonieux.

Le plus important est de garder une cohérence entre les volumes, les couleurs et les usages de chaque espace.
TEXT,
            ],
            [
                'title' => 'Sécuriser son logement : les travaux prioritaires',
                'category' => 'Sécurité',
                'isPublished' => true,
                'publishedAt' => '-2 days',
                'content' => <<<TEXT
La sécurité d’un logement passe par plusieurs éléments essentiels.

La porte d’entrée, les fenêtres, les serrures, l’éclairage extérieur et les systèmes d’alarme jouent un rôle important.

Avant d’investir dans des équipements coûteux, il est conseillé de demander l’avis d’un professionnel.

Un artisan spécialisé pourra identifier les points faibles de votre logement et proposer des solutions adaptées.
TEXT,
            ],
            [
                'title' => 'Préparer son projet de travaux étape par étape',
                'category' => 'Conseils travaux',
                'isPublished' => true,
                'publishedAt' => '-1 day',
                'content' => <<<TEXT
Un projet de travaux doit être préparé avec méthode.

Commencez par définir clairement votre besoin : rénovation complète, réparation, amélioration énergétique ou simple aménagement.

Ensuite, établissez un budget réaliste et contactez plusieurs professionnels pour obtenir des devis.

Plus votre demande est précise, plus les artisans pourront vous répondre efficacement. Photos, plans, dimensions et contraintes techniques sont très utiles.
TEXT,
            ],
        ];

        foreach ($posts as $data) {
            $slug = $this->createSlug($data['title']);

            $post = $this->blogPostRepository->findOneBy([
                'slug' => $slug,
            ]);

            if (!$post) {
                $post = new BlogPost();
                $post->setCreatedAt($now);

                $manager->persist($post);
            }

            $categorySlug = $this->createSlug($data['category']);

            $post->setTitle($data['title']);
            $post->setSlug($slug);
            $post->setContent($data['content']);
            $post->setIsPublished($data['isPublished']);
            $post->setPublishedAt(new \DateTimeImmutable($data['publishedAt']));
            $post->setUpdatedAt($now);
            $post->setCategory($categoryEntities[$categorySlug] ?? null);
            $post->setUser(null);
        }

        $manager->flush();
    }

    private function createSlug(string $value): string
    {
        return $this->slugger
            ->slug($value)
            ->lower()
            ->toString();
    }
}
