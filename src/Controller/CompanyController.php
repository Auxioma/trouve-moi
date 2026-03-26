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
            'slug' => '[a-zA-Z0-9\-]+',
            'step' => '\d+',
        ],
        defaults: ['step' => 1],
    )]
    public function askQuote(
        string $codePostal,
        string $ville,
        string $slug,
        int $step,
        Request $request,
        SessionInterface $session,
        ActivityRepository $activityRepository,
        ServicesRepository $servicesRepository,
        UserRepository $userRepository,
        MailerInterface $mailer,
    ): Response {
        $maxStep = 5;

        if ($step < 1 || $step > $maxStep) {
            throw $this->createNotFoundException();
        }
        
        $dto = $this->buildDtoFromSession($session, $activityRepository, $servicesRepository);
        $activity = $userRepository->findOneBy(['slug' => $slug]);
        $activity = $activity->getActivity();

        $dto->activity = $activity;

        $form = $this->createForm(QuoteRequestType::class, $dto, [
            'step' => $step,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('quote_request_data', [
                'activity' => $dto->activity?->getId(),
                'services' => array_map(
                    static fn ($service) => $service->getId(),
                    $dto->services ?? []
                ),
                'requestType' => $dto->requestType,
                'isUrgent' => $dto->isUrgent,
                'description' => $dto->description,
                'address' => $dto->address,
                'postalCode' => $dto->postalCode,
                'city' => $dto->city,
                'accessDetails' => $dto->accessDetails,
                'desiredDelay' => $dto->desiredDelay,
                'budget' => $dto->budget,
                'firstName' => $dto->firstName,
                'lastName' => $dto->lastName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'contactPreference' => $dto->contactPreference,
            ]);

            if ($step < $maxStep) {
                return $this->redirectToRoute('app_ask_quote', [
                    'codePostal' => $codePostal,
                    'ville' => $ville,
                    'slug' => $slug,
                    'step' => $step + 1,
                ]);
            }

            $email = (new Email())
                ->from('noreply@site.fr')
                ->to('contact@site.fr')
                ->replyTo($dto->email ?? 'noreply@site.fr')
                ->subject('Nouvelle demande de devis')
                ->text(
                    "Métier : " . ($dto->activity?->getName() ?? 'Non renseigné') . "\n" .
                    "Type de besoin : " . ($dto->requestType ?? 'Non renseigné') . "\n" .
                    "Urgent : " . ($dto->isUrgent ? 'Oui' : 'Non') . "\n" .
                    "Description : " . ($dto->description ?? 'Non renseigné') . "\n" .
                    "Adresse : " . ($dto->address ?? 'Non renseigné') . "\n" .
                    "Code postal : " . ($dto->postalCode ?? 'Non renseigné') . "\n" .
                    "Ville : " . ($dto->city ?? 'Non renseigné') . "\n" .
                    "Accès : " . ($dto->accessDetails ?? 'Non renseigné') . "\n" .
                    "Délai : " . ($dto->desiredDelay ?? 'Non renseigné') . "\n" .
                    "Budget : " . ($dto->budget ?? 'Non renseigné') . "\n" .
                    "Nom : " . ($dto->firstName ?? '') . ' ' . ($dto->lastName ?? '') . "\n" .
                    "Email : " . ($dto->email ?? 'Non renseigné') . "\n" .
                    "Téléphone : " . ($dto->phone ?? 'Non renseigné') . "\n" .
                    "Préférence de contact : " . ($dto->contactPreference ?? 'Non renseigné') . "\n"
                );

            $mailer->send($email);

            $session->remove('quote_request_data');

            return $this->redirectToRoute('app_ask_quote_success');
        }

        return $this->render('ask_quote/index.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'maxStep' => $maxStep,
            'dto' => $dto,
            'codePostal' => $codePostal,
            'ville' => $ville,
            'slug' => $slug,
        ]);
    }

    private function buildDtoFromSession(
        SessionInterface $session,
        ActivityRepository $activityRepository,
        ServicesRepository $servicesRepository,
    ): QuoteRequestDto {
        $data = $session->get('quote_request_data', []);
        $dto = new QuoteRequestDto();

        if (!empty($data['activity'])) {
            $dto->activity = $activityRepository->findOneBy($data['activity']);
        }

        if (!empty($data['services']) && is_array($data['services'])) {
            $dto->services = $servicesRepository->findBy([
                'id' => $data['services'],
            ]);
        } else {
            $dto->services = [];
        }

        $dto->requestType = $data['requestType'] ?? null;
        $dto->isUrgent = (bool) ($data['isUrgent'] ?? false);
        $dto->description = $data['description'] ?? null;
        $dto->address = $data['address'] ?? null;
        $dto->postalCode = $data['postalCode'] ?? null;
        $dto->city = $data['city'] ?? null;
        $dto->accessDetails = $data['accessDetails'] ?? null;
        $dto->desiredDelay = $data['desiredDelay'] ?? null;
        $dto->budget = $data['budget'] ?? null;
        $dto->firstName = $data['firstName'] ?? null;
        $dto->lastName = $data['lastName'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->contactPreference = $data['contactPreference'] ?? null;

        return $dto;
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