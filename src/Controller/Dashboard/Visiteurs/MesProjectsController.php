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

namespace App\Controller\Dashboard\Visiteurs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/projects', name: 'dashboard-visiteurs_')]
#[IsGranted('ROLE_ARTISAN')]
final class MesProjectsController extends AbstractController
{
    #[Route('/', name: 'liste_projects')]
    public function index(): Response
    {
        return $this->render('user/mes_projects/liste-des-projects.html.twig');
    }

    #[Route('/nouveau-projet', name: 'demarre_projects', methods: ['GET'])]
    public function demarre(): Response
    {
        return $this->render('user/mes_projects/demarre-un-projet.html.twig');
    }

    #[Route('/nouveau-projet/recapitulatif', name: 'recapitulatif', methods: ['GET'])]
    public function recapitulatif(): Response
    {
        return $this->render('user/mes_projects/recapitulatif.html.twig');
    }

    #[Route("/nouveau-projet/recapitulatif/avis", name: 'avis')]
    public function avis(): Response
    {
        return $this->render('user/mes_projects/avis.html.twig');
    }
}
