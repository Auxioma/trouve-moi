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

namespace App\Controller\Dashboard\Visiteurs;

use App\Form\ProfileType;
use App\Form\Visiteurs\Profile\ProfileVisiteursType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileUserController extends AbstractController
{
    #[Route('/user/profile', name: 'app_dashboard_user_profile_user')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $findUser = $userRepository->find($user->getId());
        $form = $this->createForm(ProfileVisiteursType::class, $findUser);

        return $this->render('dashboard/user/profile_user/profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
