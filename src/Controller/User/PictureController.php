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

use App\Form\ImageProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PictureController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/user/picture', name: 'app_user_picture')]
    #[IsGranted('ROLE_ARTISAN')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sessionUser = $this->getUser();
        $user = $this->userRepository->find($sessionUser->getId());

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ImageProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre photo de profil a été mise à jour avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/picture/index.html.twig', [
            'showUser' => $user,
            'form' => $form->createView(),
        ]);
    }
}
