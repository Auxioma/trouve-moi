<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ){}

    #[Route('/{codePostal}/{ville}/{slug}', name: 'app_company', requirements: [
        'codePostal' => '\d{5}',
        'ville' => '[a-zA-ZÀ-ÿ\-]+',
        'slug' => '[a-zA-Z0-9\-]+'
    ])]
    public function index(string $slug): Response
    {
        $compagny = $this->userRepository->findOneBy(['slug' => $slug]);

        return $this->render('company/index.html.twig', [
            'item' => $compagny,
        ]);
    }
}
