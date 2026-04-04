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

namespace App\Controller\User\Offer;

use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PackageController extends AbstractController
{
    #[Route('/{code}/{billing}/{id}', name: 'app_user_offer_basket', methods: ['GET'], requirements: ['billing' => 'monthly|yearly'])]
    public function basket(
        string $code,
        string $billing,
        int $id,
        PlanRepository $planRepository,
    ): Response {
        $plan = $planRepository->find($id);

        if (!$plan) {
            throw $this->createNotFoundException('Le pack demandé est introuvable.');
        }

        $price = match ($billing) {
            'monthly' => $plan->getPriceMonthly(),
            'yearly' => $plan->getPriceYearly(),
            default => null,
        };

        if (null === $price) {
            throw $this->createNotFoundException('Cette formule de facturation n’est pas disponible pour ce pack.');
        }

        return $this->render('user/offer/package/billing.html.twig', [
            'plan' => $plan,
            'billing' => $billing,
            'price' => $price,
            'billingLabel' => 'monthly' === $billing ? 'Mensuel' : 'Annuel',
            'features' => $plan->getFeatures() ?? [],
        ]);
    }

    #[Route('/user/offer/package', name: 'app_user_offer_package')]
    #[IsGranted('ROLE_ARTISAN')]
    public function index(PlanRepository $planRepository): Response
    {
        return $this->render('user/offer/package/index.html.twig', [
            'plans' => $planRepository->findAll(),
        ]);
    }
}
