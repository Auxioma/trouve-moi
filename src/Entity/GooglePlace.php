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

namespace App\Entity;

use App\Repository\GooglePlaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GooglePlaceRepository::class)]
class GooglePlace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $placeId = null;

    #[ORM\Column(length: 255)]
    private ?string $displayName = null;

    #[ORM\Column(type: 'float')]
    private ?float $rating = null;

    #[ORM\Column(type: 'json')]
    private ?array $reviews = null;

    #[ORM\Column]
    private ?int $reviewsCount = null;

    #[ORM\Column(length: 255)]
    private ?string $googleMapsUri = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastSyncAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(string $placeId): static
    {
        $this->placeId = $placeId;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviews(): ?array
    {
        return $this->reviews;
    }

    public function setReviews(array $reviews): static
    {
        $this->reviews = $reviews;

        return $this;
    }

    public function getReviewsCount(): ?int
    {
        return $this->reviewsCount;
    }

    public function setReviewsCount(int $reviewsCount): static
    {
        $this->reviewsCount = $reviewsCount;

        return $this;
    }

    public function getGoogleMapsUri(): ?string
    {
        return $this->googleMapsUri;
    }

    public function setGoogleMapsUri(string $googleMapsUri): static
    {
        $this->googleMapsUri = $googleMapsUri;

        return $this;
    }

    public function getLastSyncAt(): ?\DateTimeImmutable
    {
        return $this->lastSyncAt;
    }

    public function setLastSyncAt(\DateTimeImmutable $lastSyncAt): static
    {
        $this->lastSyncAt = $lastSyncAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
