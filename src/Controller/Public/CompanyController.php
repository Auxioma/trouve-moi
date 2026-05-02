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

namespace App\Controller\Public;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ){}

    #[Route('/{category}/{city}/{slug}', name: 'deail_entreprise')]
    #[Route('/nnnnnnnnnnnnnnnnn', name: 'deail_entqsqsqsqsreprise')]
    public function detailCompagny(string $slug): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'item' => $this->userRepository->findOneBy(['slug' => $slug]),
        ]);
    }
}
