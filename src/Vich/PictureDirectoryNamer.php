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

namespace App\Vich;

use App\Entity\Pictures;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

final class PictureDirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName(object|array $object, PropertyMapping $mapping): string
    {
        if (\is_array($object)) {
            throw new \RuntimeException('Le directory namer attend un objet, pas un tableau.');
        }

        if (!$object instanceof Pictures) {
            throw new \RuntimeException(\sprintf('Le directory namer attend une instance de %s, %s reçu.', Pictures::class, $object::class));
        }

        $user = $object->getUser();

        if (null === $user) {
            return 'tmp';
        }

        $userId = $user->getId();

        if (null === $userId) {
            return 'tmp';
        }

        return implode('/', mb_str_split((string) $userId));
    }
}
