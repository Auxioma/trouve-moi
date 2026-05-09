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

namespace App\Controller\Authentification;

use App\Entity\User;
use App\Form\Authentification\Artisan\ArtisanRegistrationType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/souscription', name: 'artisan_registration_')]
class RegistrationArtisanController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly EmailVerifier $emailVerifier,
    ) {
    }

    #[Route('/pro', name: 'home')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
    ): Response {
        $user = new User();

        $form = $this->createForm(ArtisanRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = mb_strtolower(mb_trim((string) $form->get('email')->getData()));
            $siret = mb_trim((string) $form->get('siret')->getData());
            $plainPassword = (string) $form->get('plainPassword')->getData();

            $existingEmail = $this->userRepository->findOneBy([
                'email' => $email,
            ]);

            if ($existingEmail instanceof User) {
                $this->addFlash('warning', 'Un compte existe déjà avec cette adresse email.');

                return $this->redirectToRoute('artisan_registration_home');
            }

            $existingSiret = $this->userRepository->findOneBy([
                'siret' => $siret,
            ]);

            if ($existingSiret instanceof User) {
                $existingSiret->setEmail($email);
                $existingSiret->setPassword(
                    $userPasswordHasher->hashPassword($existingSiret, $plainPassword)
                );
                $existingSiret->setRoles(['ROLE_ARTISAN']);

                $this->entityManager->flush();

                $this->addFlash('success', 'Votre compte artisan a été mis à jour avec succès.');
            } else {
                $user->setEmail($email);
                $user->setSiret($siret);
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );
                $user->setRoles(['ROLE_ARTISAN']);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre compte artisan a été créé avec succès.');
            }

            return $this->redirectToRoute('user_registration_app_creez_votre_compte_confirmation');
        }

        return $this->render('authentification/artisan/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
