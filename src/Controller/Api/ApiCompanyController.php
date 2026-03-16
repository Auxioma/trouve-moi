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

            if (200 !== $response->getStatusCode()) {
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

            return $this->json([
                'success' => true,
                'data' => [
                    'siren' => $company['siren'] ?? '',
                    'name' => $company['nom_complet'] ?? '',
                    'siret' => $siege['siret'] ?? '',
                    'address' => $siege['adresse'] ?? '',
                    'postal_code' => $siege['code_postal'] ?? '',
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
            ], 500);
        }
    }
}