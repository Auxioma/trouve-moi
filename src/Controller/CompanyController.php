<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    #[Route('/{codePostal}/{ville}/{slug}', name: 'app_company', requirements: [
        'codePostal' => '\d{5}',
        'ville' => '[a-zA-ZÀ-ÿ\-]+',
        'slug' => '[a-zA-Z0-9\-]+'
    ])]
    public function index(): Response
    {
        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }
}
