<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GooglePlaceService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $googleMapsApiKey,
    ) {
    }

    /**
     * Recherche un place_id à partir d'un texte.
     */
    public function findPlaceByText(string $query): ?array
    {
        try {
            // Version simple en Legacy GET, très pratique côté backend Symfony
            $response = $this->httpClient->request('GET', 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
                'query' => [
                    'input' => $query,
                    'inputtype' => 'textquery',
                    'fields' => 'place_id,name',
                    'key' => $this->googleMapsApiKey,
                ],
            ]);

            $data = $response->toArray(false);

            if (
                !isset($data['candidates']) ||
                !is_array($data['candidates']) ||
                empty($data['candidates'])
            ) {
                return null;
            }

            $candidate = $data['candidates'][0];

            return [
                'place_id' => $candidate['place_id'] ?? null,
                'name' => $candidate['name'] ?? null,
            ];
        } catch (ExceptionInterface) {
            return null;
        }
    }

    /**
     * Récupère les détails d'un lieu Google.
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://maps.googleapis.com/maps/api/place/details/json', [
                'query' => [
                    'place_id' => $placeId,
                    'fields' => 'name,rating,reviews,url,user_ratings_total',
                    'key' => $this->googleMapsApiKey,
                ],
            ]);

            $data = $response->toArray(false);

            if (!isset($data['result']) || !is_array($data['result'])) {
                return null;
            }

            $result = $data['result'];

            $reviews = array_map(static function (array $review): array {
                return [
                    'author_name' => $review['author_name'] ?? 'Utilisateur',
                    'rating' => $review['rating'] ?? 0,
                    'text' => $review['text'] ?? '',
                    'relative_time_description' => $review['relative_time_description'] ?? '',
                    'profile_photo_url' => $review['profile_photo_url'] ?? null,
                    'author_url' => $review['author_url'] ?? null,
                ];
            }, $result['reviews'] ?? []);

            return [
                'display_name' => $result['name'] ?? null,
                'rating' => isset($result['rating']) ? (float) $result['rating'] : 0.0,
                'reviews_count' => isset($result['user_ratings_total']) ? (int) $result['user_ratings_total'] : count($reviews),
                'reviews' => $reviews,
                'google_maps_uri' => $result['url'] ?? sprintf('https://www.google.com/maps/place/?q=place_id:%s', $placeId),
            ];
        } catch (ExceptionInterface) {
            return null;
        }
    }
}
