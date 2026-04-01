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

namespace App\Controller\User;

use App\Entity\Enum\UserProfileStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_ARTISAN')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (UserProfileStatus::PARTIAL === $user->getProfileStatus()) {
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
