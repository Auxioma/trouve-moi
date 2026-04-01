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
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/user/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_ARTISAN')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sessionUser = $this->getUser();
        $user = $this->userRepository->find($sessionUser->getId());

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $services = $form->get('services')->getData();

            // Optionnel : reset si besoin (évite doublons / incohérences)
            $user->getServices();

            foreach ($services as $service) {
                $user->addService($service);
            }

            $user->setProfileStatus(UserProfileStatus::VALIDATED);

            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/profile/index.html.twig', [
            'showUser' => $user,
            'form' => $form->createView(),
        ]);
    }
}
