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

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, HttpClientInterface $httpClient): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_ARTISAN']);

            /**
             * exemple de URL
             * https://recherche-entreprises.api.gouv.fr/search?q=477834782
             * si le numéro de siren est faux ecrire un message flash pour dire au client que le siren est invalide
             * autrement, j'ai besoin
             * du nom, du prénom, du nom de l'entreprise ou le nom de la compagny, la géolocalisation, l'adresse, code postal, ville.
             */
            $siren = preg_replace('/\D/', '', (string) $form->get('siren')->getData());

            if (!$siren || !preg_match('/^\d{9}$/', $siren)) {
                $this->addFlash('danger', 'Le numéro SIREN est invalide.');

                return $this->redirectToRoute('app_register');
            }

            try {
                $response = $httpClient->request('GET', 'https://recherche-entreprises.api.gouv.fr/search', [
                    'query' => [
                        'q' => $siren,
                        'page' => 1,
                        'per_page' => 1,
                    ],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]);

                if (200 !== $response->getStatusCode()) {
                    $this->addFlash('danger', 'Impossible de vérifier le numéro SIREN pour le moment.');

                    return $this->redirectToRoute('app_register');
                }

                $data = $response->toArray(false);

                if (
                    !isset($data['results'])
                    || !\is_array($data['results'])
                    || 0 === \count($data['results'])
                ) {
                    $this->addFlash('danger', 'Le numéro SIREN est invalide ou introuvable.');

                    return $this->redirectToRoute('app_register');
                }

                $company = $data['results'][0];

                // Vérification supplémentaire : s'assurer que le SIREN retourné correspond bien
                $returnedSiren = $company['siren'] ?? null;
                if ($returnedSiren !== $siren) {
                    $this->addFlash('danger', 'Le numéro SIREN est invalide ou ne correspond à aucune entreprise.');

                    return $this->redirectToRoute('app_register');
                }

                /**
                 * Mapping des données
                 * Adapte les noms de setters selon ton entité User.
                 */

                // Nom société / entreprise
                $companyName = $company['nom_commercial']
                                ?? $company['nom_raison_sociale']
                                ?? ($company['liste_enseignes'][0] ?? null);
                
                if ($companyName && method_exists($user, 'setCompagny')) {
                    $user->setCompagny($companyName);
                }

                // Dirigeant / personne physique (si disponible)
                if (isset($company['dirigeants'][0]) && \is_array($company['dirigeants'][0])) {
                    $leader = $company['dirigeants'][0];

                    $firstName = $leader['prenoms'] ?? null;
                    $lastName = $leader['nom'] ?? null;

                    if ($firstName && method_exists($user, 'setFirstName') && !$user->getFirstName()) {
                        $user->setFirstName($firstName);
                    }

                    if ($lastName && method_exists($user, 'setLastName') && !$user->getLastName()) {
                        $user->setLastName($lastName);
                    }
                }

                // Adresse
                $address = $company['siege']['adresse'] ?? null;
                $postalCode = $company['siege']['code_postal'] ?? null;
                $city = $company['siege']['libelle_commune'] ?? null;

                if ($address && method_exists($user, 'setAddress')) {
                    $user->setAddress($address);
                }

                if ($postalCode && method_exists($user, 'setPostalCode')) {
                    $user->setPostalCode($postalCode);
                }

                if ($city && method_exists($user, 'setCity')) {
                    $user->setCity($city);
                }

                // Tu peux stocker le SIREN si tu as un champ
                if (method_exists($user, 'setSiren')) {
                    $user->setSiren($siren);
                }

                /*
                 * Géolocalisation via API Adresse (optionnel)
                 */
                if ($address && $postalCode && $city) {
                    $fullAddress = mb_trim($address.' '.$postalCode.' '.$city);

                    try {
                        $geoResponse = $httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/', [
                            'query' => [
                                'q' => $fullAddress,
                                'limit' => 1,
                            ],
                            'headers' => [
                                'Accept' => 'application/json',
                            ],
                        ]);

                        if (200 === $geoResponse->getStatusCode()) {
                            $geoData = $geoResponse->toArray(false);

                            if (
                                isset($geoData['features'][0]['geometry']['coordinates'])
                                && \is_array($geoData['features'][0]['geometry']['coordinates'])
                                && 2 === \count($geoData['features'][0]['geometry']['coordinates'])
                            ) {
                                $longitude = $geoData['features'][0]['geometry']['coordinates'][0];
                                $latitude = $geoData['features'][0]['geometry']['coordinates'][1];

                                if (method_exists($user, 'setLatitude')) {
                                    $user->setLatitude((string) $latitude);
                                }

                                if (method_exists($user, 'setLongitude')) {
                                    $user->setLongitude((string) $longitude);
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        // On ignore l’erreur de géolocalisation pour ne pas bloquer l’inscription
                    }
                }
            } catch (\Throwable $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la vérification du SIREN.');

                return $this->redirectToRoute('app_register');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('hello@hello.hello', 'Ace Mail Bot'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationPro' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
