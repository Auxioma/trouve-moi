<?php

namespace App\Controller\Visiteurs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/visiteurs/dashboard', name: 'app_visiteurs_dashboard')]
    public function index(): Response
    {
        return $this->render('visiteurs/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
