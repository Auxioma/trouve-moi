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

use App\Repository\TestimonialRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TestimonialRepository $testimonialRepository,
        private readonly CacheInterface $cache, // 👈 ajout
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $latestUsers = $this->cache->get('home.latest_users', function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 heure

            return $this->userRepository->findLatestArtisans(4);
        });

        $testimonials = $this->cache->get('home.testimonials', function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 heure

            return $this->testimonialRepository->findBy([], ['createdAt' => 'DESC'], 20);
        });

        return $this->render('home/home.html.twig', [
            'localisationArtisants' => $latestUsers,
            'testimonials' => $testimonials,
        ]);
    }
}
