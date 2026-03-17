<?php

namespace App\Entity\Enum;

enum UserProfileStatus: string
{
    case PARTIAL = 'profil_partiel';
    case VALIDATED = 'profil_valide';
    case BANNED = 'profil_banni';
}