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

use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ActivitySearchController extends AbstractController
{
    #[Route('/ajax/search/activity', name: 'ajax_search_activity', methods: ['GET'])]
    public function __invoke(Request $request, ActivityRepository $activityRepository): JsonResponse
    {
        $term = mb_trim((string) $request->query->get('q', ''));

        if (mb_strlen($term) < 2) {
            return $this->json([]);
        }

        $activities = $activityRepository->createQueryBuilder('a')
            ->andWhere('LOWER(a.name) LIKE LOWER(:term)')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('a.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $results = [];

        foreach ($activities as $activity) {
            $results[] = [
                'id' => $activity->getId(),
                'name' => $activity->getName(),
                'slug' => $activity->getSlug(),
            ];
        }

        return $this->json($results);
    }

    #[Route('/api/cities', name: 'api_cities', methods: ['GET'])]
    public function cities(Request $request, UserRepository $userRepository): JsonResponse
    {
        $term = trim((string) $request->query->get('q', ''));

        if (mb_strlen($term) < 2) {
            return $this->json([]);
        }

        $cities = $userRepository->findDistinctCitiesByTerm($term);

        return $this->json($cities);
    }

}
