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

use App\Entity\Pictures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;

class PicturesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Clé   : nom du fichier source dans picturesTempDir
     * Valeur : seed picsum pour le téléchargement
     */
    private const PICTURES = [
        'artisan-01.jpg' => 'artisan-renovation',
        'artisan-02.jpg' => 'artisan-electricien',
        'artisan-03.jpg' => 'artisan-plombier',
        'artisan-04.jpg' => 'artisan-macon',
        'artisan-05.jpg' => 'artisan-couvreur',
    ];

    private const BATCH_SIZE = 50;

    private string $picturesTempDir;

    public function __construct(
        #[Autowire('%kernel.project_dir%/images/slider')]
        string $picturesTempDir,
    ) {
        $this->picturesTempDir = $picturesTempDir;
    }

    public function load(ObjectManager $manager): void
    {
        $this->downloadMissingImages();

        $now        = new \DateTimeImmutable();
        $imageNames = array_keys(self::PICTURES);
        $count      = 0;

        for ($i = 1; $i <= 500; ++$i) {
            $reference = \sprintf('user_artisan_%03d', $i);

            if (!$this->hasReference($reference, User::class)) {
                continue;
            }

            /** @var User $artisan */
            $artisan = $this->getReference($reference, User::class);

            /*
             * L'utilisateur DOIT avoir un ID pour que PictureDirectoryNamer
             * puisse construire le chemin (mb_str_split sur $user->getId()).
             * UserFixtures flush en batch de 50, les IDs sont donc déjà assignés.
             */
            if (null === $artisan->getId()) {
                throw new \RuntimeException(
                    \sprintf(
                        'L\'artisan "%s" n\'a pas d\'ID. Vérifier que UserFixtures flush avant PicturesFixtures.',
                        $reference
                    )
                );
            }

            foreach ($imageNames as $imageName) {
                $sourcePath = $this->picturesTempDir . '/images/slider/' . $imageName;

                /*
                 * VichUploader DÉPLACE le fichier source vers sa destination.
                 * On copie chaque image dans un fichier temporaire unique
                 * avant de le passer au File object — sinon la première entité
                 * consomme le fichier et les suivantes lèvent une FileNotFoundException.
                 */
                $tempPath = \sprintf(
                    '%s/vich_tmp_%s_%s',
                    sys_get_temp_dir(),
                    uniqid('', true),
                    $imageName
                );

                if (!copy($sourcePath, $tempPath)) {
                    throw new \RuntimeException(
                        \sprintf(
                            'Impossible de copier "%s" vers "%s".',
                            $sourcePath,
                            $tempPath
                        )
                    );
                }

                $picture = new Pictures();
                $picture->setImageFile(new File($tempPath));
                $picture->setUser($artisan);
                $picture->setUpdatedAt($now);

                $manager->persist($picture);
                ++$count;
            }

            /*
             * Flush par batch pour éviter la saturation mémoire.
             * On NE fait PAS de clear() car VichUploader a besoin
             * que les entités restent dans l'UnitOfWork jusqu'au flush
             * pour déclencher ses listeners prePersist/preUpdate.
             */
            if (0 === ($count % (self::BATCH_SIZE * \count($imageNames)))) {
                $manager->flush();
            }
        }

        $manager->flush();
    }

    private function downloadMissingImages(): void
    {
        $imagesDir = $this->picturesTempDir . '/images/slider';

        if (!is_dir($imagesDir)) {
            mkdir($imagesDir, 0755, true);
        }

        foreach (self::PICTURES as $imageName => $seed) {
            $filePath = $imagesDir . '/' . $imageName;

            if (file_exists($filePath)) {
                continue;
            }

            $url       = \sprintf('https://picsum.photos/seed/%s/800/600', $seed);
            $imageData = file_get_contents($url);

            if (false === $imageData) {
                throw new \RuntimeException(
                    \sprintf(
                        'Impossible de télécharger "%s". Lance d\'abord : bash bin/download-fixture-images.sh',
                        $imageName
                    )
                );
            }

            file_put_contents($filePath, $imageData);
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
