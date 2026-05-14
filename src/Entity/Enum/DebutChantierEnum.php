<?php

namespace App\Entity\Enum;

enum DebutChantierEnum: string
{
    case ASAP = 'asap';
    case UN_MOIS = '1_mois';
    case TROIS_MOIS = '3_mois';
}
