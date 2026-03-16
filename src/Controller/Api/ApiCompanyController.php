<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/ajax', name: 'ajax_')]
final class ApiCompanyController extends AbstractController
{
    #[Route('/company-by-siren', name: 'company_by_siren', methods: ['GET'])]
    public function getCompanyBySiren(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $siren = trim((string) $request->query->get('siren', ''));

        if (!preg_match('/^\d{9}$/', $siren)) {
            return $this->json([
                'success' => false,
                'message' => 'Le SIREN doit contenir exactement 9 chiffres.',
            ], 400);
        }

        try {
            $response = $httpClient->request(
                'GET',
                'https://recherche-entreprises.api.gouv.fr/search',
                [
                    'query' => [
                        'q' => $siren,
                    ],
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]
            );

            if ($response->getStatusCode() !== 200) {
                return $this->json([
                    'success' => false,
                    'message' => 'Impossible de récupérer les informations de l’entreprise.',
                ], $response->getStatusCode());
            }

            $data = $response->toArray(false);
            $results = $data['results'] ?? [];

            if (empty($results)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Aucune entreprise trouvée.',
                ], 404);
            }

            $company = null;

            foreach ($results as $result) {
                if (($result['siren'] ?? null) === $siren) {
                    $company = $result;
                    break;
                }
            }

            $company ??= $results[0];

            $siege = $company['siege'] ?? [];

            /*
             * Cas personne morale :
             * - nom_complet / nom_raison_sociale / nom_entreprise
             *
             * Cas entrepreneur individuel :
             * - prenom
             * - nom
             */
            $firstName = $company['prenom'] ?? '';
            $lastName = $company['nom'] ?? '';

            $companyName = $company['nom_complet']
                ?? $company['nom_raison_sociale']
                ?? $company['nom_entreprise']
                ?? trim($firstName . ' ' . $lastName);

            return $this->json([
                'success' => true,
                'data' => [
                    'siren' => $company['siren'] ?? '',
                    'siret' => $siege['siret'] ?? '',
                    'companyName' => $companyName,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'address' => $siege['adresse'] ?? '',
                    'postalCode' => $siege['code_postal'] ?? '',
                    'city' => $siege['libelle_commune'] ?? '',
                ],
            ]);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur réseau lors de l’appel API.',
            ], 502);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur serveur interne.',
                'error' => $e->getMessage(), // à retirer en prod si tu veux
            ], 500);
        }
    }
}