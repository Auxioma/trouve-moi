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
use Stripe\Checkout\Session;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PackageController extends AbstractController
{
    #[Route('/{code}/{billing}/{id}', name: 'app_user_offer_basket', methods: ['GET'], requirements: ['billing' => 'monthly|yearly'])]
    public function basket(
        string $code,
        string $billing,
        int $id,
        PlanRepository $planRepository
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

        if ($price === null) {
            throw $this->createNotFoundException('Cette formule de facturation n’est pas disponible pour ce pack.');
        }

        return $this->render('user/offer/package/billing.html.twig', [
            'plan' => $plan,
            'billing' => $billing,
            'price' => $price,
            'billingLabel' => $billing === 'monthly' ? 'Mensuel' : 'Annuel',
            'features' => $plan->getFeatures() ?? [],
        ]);
    }
    
    #[Route('/user/offer/package', name: 'app_user_offer_package')]
    #[IsGranted('ROLE_ARTISAN')]
    public function index(PlanRepository $planRepository): Response
    {
        return $this->render('user/offer/package/index.html.twig', [
            'plans' => $planRepository->findAll()
        ]);
    }

    #[Route('/user/offer/paiement', name: 'app_user_offer_paiement', methods: ['POST'])]
    #[IsGranted('ROLE_ARTISAN')]
    public function paiement(Request $request, PlanRepository $planRepository): Response
    {
        $planId = $request->request->get('plan_id');
        $billing = $request->request->get('billing');

        if (!$planId) {
            throw $this->createNotFoundException('Aucun plan sélectionné.');
        }

        if (!in_array($billing, ['monthly', 'yearly'], true)) {
            throw $this->createNotFoundException('Périodicité invalide.');
        }

        $plan = $planRepository->find($planId);

        if (!$plan) {
            throw $this->createNotFoundException('Le pack sélectionné est introuvable.');
        }

        $price = match ($billing) {
            'monthly' => $plan->getPriceMonthly(),
            'yearly' => $plan->getPriceYearly(),
        };

        if ($price === null || $price <= 0) {
            throw new \RuntimeException('Le prix du plan est invalide.');
        }

        $interval = $billing === 'monthly' ? 'month' : 'year';

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $successUrl = $this->generateUrl(
            'stripe_success',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        ) . '?session_id={CHECKOUT_SESSION_ID}';

        $cancelUrl = $this->generateUrl(
            'stripe_cancel',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $session = Session::create([
            'mode' => 'subscription',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) round($price * 100),
                    'recurring' => [
                        'interval' => $interval,
                    ],
                    'product_data' => [
                        'name' => $plan->getName(),
                        'description' => $billing === 'monthly'
                            ? 'Abonnement mensuel'
                            : 'Abonnement annuel',
                    ],
                ],
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        if (empty($session->url)) {
            throw new \RuntimeException('Stripe n’a pas retourné d’URL de paiement.');
        }

        return $this->redirect($session->url, 303);
    }
    
    #[Route('/user/offer/paiement/sucess', name: 'stripe_success')]
    public function success(Request $request): Response
    {
        return $this->render('user/offer/package/success.html.twig');
    }

    #[Route('/user/offer/paiement/cancel', name: 'stripe_cancel')]
    public function cancel(Request $request): Response
    {
        return $this->render('user/offer/package/success.html.twig');
    }

}
