<?php

namespace App\Controller\User;

use App\Form\ImageProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PictureController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ){}

    #[Route('/user/picture', name: 'app_user_picture')]
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
