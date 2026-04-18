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

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SlugExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('slug', [$this, 'slugify']),
        ];
    }

    public function slugify(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $value = mb_strtolower($value, 'UTF-8');

        $replacements = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'œ' => 'oe',
            'æ' => 'ae',
        ];

        $value = strtr($value, $replacements);

        // remplace tout ce qui n'est pas lettre/chiffre par -
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);

        // supprime les - au début / fin
        $value = mb_trim($value, '-');

        return $value;
    }
}
