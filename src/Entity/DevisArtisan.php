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

use App\Repository\DevisArtisanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisArtisanRepository::class)]
class DevisArtisan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'devisArtisans')]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(inversedBy: 'devisArtisans')]
    private ?User $artisan = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sendAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $viewAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $answerAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    public function getArtisan(): ?User
    {
        return $this->artisan;
    }

    public function setArtisan(?User $artisan): static
    {
        $this->artisan = $artisan;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSendAt(): ?\DateTimeImmutable
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeImmutable $sendAt): static
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getViewAt(): ?\DateTimeImmutable
    {
        return $this->viewAt;
    }

    public function setViewAt(\DateTimeImmutable $viewAt): static
    {
        $this->viewAt = $viewAt;

        return $this;
    }

    public function getAnswerAt(): ?\DateTimeImmutable
    {
        return $this->answerAt;
    }

    public function setAnswerAt(\DateTimeImmutable $answerAt): static
    {
        $this->answerAt = $answerAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
