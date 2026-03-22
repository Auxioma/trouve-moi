<?php

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
            throw new \RuntimeException(sprintf(
                'Le directory namer attend une instance de %s, %s reçu.',
                Pictures::class,
                $object::class
            ));
        }

        $user = $object->getUser();

        if ($user === null) {
            return 'tmp';
        }

        $userId = $user->getId();

        if ($userId === null) {
            return 'tmp';
        }

        return implode('/', str_split((string) $userId));
    }
}