<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(): Response
    {
        return $this->render('user/profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
