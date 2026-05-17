<?php

namespace App\Controller\Authentification;

use App\Entity\User;
use App\Form\Authentification\User\UserRegistrationType;
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
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/souscription', name: 'user_registration_')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly EmailVerifier $emailVerifier,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('authentification/registration/register.html.twig');
    }

    #[Route('/particuliers', name: 'app_creez-votre-compte')]
    public function votreCompte(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $particulier = new User();

        $form = $this->createForm(UserRegistrationType::class, $particulier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $existingUser = $this->userRepository->findOneBy([
                'email' => $email,
            ]);

            if ($existingUser instanceof User) {
                $this->addFlash('warning', 'Un compte existe déjà avec cette adresse. Connectez-vous ici.');

                return $this->redirectToRoute('user_registration_app_creez-votre-compte');
            }

            /** @var string $plainPassword */
            $plainPassword = (string) $form->get('plainPassword')->getData();

            $particulier->setEmail($email);
            $particulier->setPassword(
                $userPasswordHasher->hashPassword($particulier, $plainPassword)
            );
            $particulier->setRoles(['ROLE_USER']);

            $entityManager->persist($particulier);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'user_registration_app_verify_email',
                $particulier,
                (new TemplatedEmail())
                    ->from(new Address('hello@trouvemoi.eu', 'Trouvemoi.eu'))
                    ->to((string) $particulier->getEmail())
                    ->subject('Confirmez votre adresse email')
                    ->htmlTemplate('emails/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('user_registration_app_creez_votre_compte_confirmation');
        }

        return $this->render('authentification/registration/votre-compte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/particuliers/confirmation', name: 'app_creez_votre_compte_confirmation', priority: 10)]
    public function votreCompteConfirmation(): Response
    {
        return $this->render('authentification/registration/confirmation.html.twig');
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var User $user */
            $user = $this->getUser();

            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash(
                'verify_email_error',
                $translator->trans($exception->getReason(), [], 'VerifyEmailBundle')
            );

            return $this->redirectToRoute('user_registration_app_register');
        }

        $this->addFlash('success', 'Votre adresse email a bien été vérifiée.');

        return $this->redirectToRoute('user_registration_app_register');
    }
}
