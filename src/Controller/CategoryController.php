<?php

/**
 * Copyright (c) 2026 Auxioma Web Agency
 * https://trouvemoi.eu
 *
 * Ce fichier fait partie du projet Trouvemoi.eu développé par Auxioma Web Agency.
 * Tous droits réservés.
 *
 * Ce code source, son architecture, sa structure, ses scripts et ses composants
 * sont la propriété exclusive de Auxioma Web Agency et de ses partenaires.
 *
 * Toute reproduction, modification, distribution, publication ou utilisation,
 * totale ou partielle, sans autorisation écrite préalable est strictement interdite.
 *
 * Ce code est confidentiel et propriétaire.
 * Droit applicable : Monde.
 */

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {}
    #[Route('/recherche', name: 'app_category')]
    public function index(Request $request): Response
    {

        $array = $request->query->all();
       
        $activity = $array['activity'] ?? null;
        $ville = $array['location'] ?? null;
        $latitude = $array['latitude'] ?? null;
        $longitude = $array['longitude'] ?? null;

        // je vais chercher les utilisateurs qui ont une activité et une ville correspondante
        $users = $this->userRepository->findByActivityAndCity($activity, $ville, $latitude, $longitude);

        dd($users);

        return $this->render('category/category.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
}
