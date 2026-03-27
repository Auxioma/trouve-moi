<?php

namespace App\Controller;

use App\Dto\QuoteRequestDto;
use App\Form\QuoteRequestType;
use App\Repository\ActivityRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route(
        '/{codePostal}/{ville}/{slug}/devis-en-ligne/{step}',
        name: 'app_ask_quote',
        requirements: [
            'codePostal' => '\d{5}',
            'ville' => '[a-zA-ZÀ-ÿ\-]+',
            'slug' => '[a-zA-Z0-9\-]+'
        ],
        defaults: ['step' => 1],
    )]
    public function askQuote(
        string $codePostal,
        string $ville,
        string $slug,
        Request $request,
        SessionInterface $session,
        ActivityRepository $activityRepository,
        ServicesRepository $servicesRepository,
        UserRepository $userRepository,
        MailerInterface $mailer,
    ): Response {

        $quote = new QuoteRequestDto();
        $form = $this->createForm(QuoteRequestType::class, $quote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('app_ask_quote_success');
        }

        return $this->render('ask_quote/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/{codePostal}/{ville}/{slug}',
        name: 'app_company',
        requirements: [
            'codePostal' => '\d{5}',
            'ville' => '[a-zA-ZÀ-ÿ\-]+',
            'slug' => '[a-zA-Z0-9\-]+',
        ]
    )]
    public function showCompany(
        string $codePostal,
        string $ville,
        string $slug
    ): Response {
        $company = $this->userRepository->findOneBy([
            'slug' => $slug,
        ]);

        if (!$company) {
            throw $this->createNotFoundException('Entreprise introuvable.');
        }

        return $this->render('company/show.html.twig', [
            'item' => $company,
            'codePostal' => $codePostal,
            'ville' => $ville,
            'slug' => $slug,
        ]);
    }

    #[Route('/demande-devis/succes', name: 'app_ask_quote_success')]
    public function success(): Response
    {
        return $this->render('ask_quote/success.html.twig');
    }
}