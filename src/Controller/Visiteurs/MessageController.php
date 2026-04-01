<?php

namespace App\Controller\Visiteurs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MessageController extends AbstractController
{
    #[Route('/visiteurs/message', name: 'app_visiteurs_message')]
    public function index(): Response
    {
        return $this->render('visiteurs/message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }
}
