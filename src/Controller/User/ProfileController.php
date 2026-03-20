<?php

namespace App\Controller\User;

use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ){}
    
    #[Route('/user/profile', name: 'app_user_profile')]
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
