<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ){}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        /* Je vais prendre les 10 derniers utilisateurs créés pour les afficher sur la page d'accueil */
        $latestUsers = $this->userRepository->findLatestArtisans(10);

        return $this->render('home/index.html.twig', [
            'lastUser' => $latestUsers,
        ]);
    }
}
