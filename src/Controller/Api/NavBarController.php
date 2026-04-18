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

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class NavBarController extends AbstractController
{

    public function __construct(
        private RequestStack $requestStack
    ) {}

    public function NavBar(): Response
    {
        $request = $this->requestStack->getMainRequest();
        $route = $request?->attributes->get('_route');

        if ($route === 'app_home') {
            return $this->render('_partials/navbar.html.twig', [
                'isHome' => true,
            ]);
        }

        return $this->render('_partials/navbar.html.twig',[
            'isHome' => false,
        ]);
    }
}
