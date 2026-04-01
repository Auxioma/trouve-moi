<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\VisiteurLoginType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SecurityController extends AbstractController
{

    public function __construct(private EmailVerifier $emailVerifier)
    {
    }
    
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/nav-tab-login', name: 'app_nav_tab_login')]
    public  function NavTabLogin(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $newVisiteur = new User();
        $Visiteur = $this->createForm(VisiteurLoginType::class, $newVisiteur, [
            'action' => $this->generateUrl('app_nav_tab_login'),
            'method' => 'POST',
        ]);
        $Visiteur->handleRequest($request);

        if ($Visiteur->isSubmitted() && $Visiteur->isValid()) {
            
             $plainPassword = $Visiteur->get('plainPassword')->getData();

            // encode the plain password
            $newVisiteur->setPassword($userPasswordHasher->hashPassword($newVisiteur, $plainPassword));

            $newVisiteur->setRoles(['ROLE_USER']);
            $em->persist($newVisiteur);
            $em->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $newVisiteur,
                (new TemplatedEmail())
                    ->from(new Address('hello@hello.hello', 'Ace Mail Bot'))
                    ->to((string) $newVisiteur->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('_partials/registrationParticulier.html.twig', [
            'registrationParticulier' => $Visiteur->createView(),
        ]);
    }
}
