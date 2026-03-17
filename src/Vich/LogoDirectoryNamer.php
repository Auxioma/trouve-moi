<?php

namespace App\Vich;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

final class LogoDirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName(object|array $object, PropertyMapping $mapping): string
    {
        if (is_array($object)) {
            throw new \RuntimeException('Le directory namer attend un objet, pas un tableau.');
        }

        if (!method_exists($object, 'getId')) {
            throw new \RuntimeException(sprintf(
                'La classe "%s" doit avoir une méthode getId().',
                $object::class
            ));
        }

        $id = $object->getId();

        if (null === $id) {
            return 'tmp';
        }

        return implode('/', str_split((string) $id));
    }
}