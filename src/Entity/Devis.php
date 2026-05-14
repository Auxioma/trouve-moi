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

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enum\DebutChantierEnum;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $visiteur = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $surface = null;

    #[ORM\Column(length: 255)]
    private ?string $budget = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(enumType: DebutChantierEnum::class)]
    private ?DebutChantierEnum $debutChantier = null;

    /**
     * @var Collection<int, DevisImage>
     */
    #[ORM\OneToMany(targetEntity: DevisImage::class, mappedBy: 'devis')]
    private Collection $devisImages;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Activity $metier = null;

    /**
     * @var Collection<int, DevisArtisan>
     */
    #[ORM\OneToMany(targetEntity: DevisArtisan::class, mappedBy: 'devis')]
    private Collection $devisArtisans;

    public function __construct()
    {
        $this->devisImages = new ArrayCollection();
        $this->devisArtisans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisiteur(): ?User
    {
        return $this->visiteur;
    }

    public function setVisiteur(?User $visiteur): static
    {
        $this->visiteur = $visiteur;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSurface(): ?string
    {
        return $this->surface;
    }

    public function setSurface(string $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(string $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDebutChantier(): ?DebutChantierEnum
    {
        return $this->debutChantier;
    }

    public function setDebutChantier(?DebutChantierEnum $debutChantier): static
    {
        $this->debutChantier = $debutChantier;

        return $this;
    }

    /**
     * @return Collection<int, DevisImage>
     */
    public function getDevisImages(): Collection
    {
        return $this->devisImages;
    }

    public function addDevisImage(DevisImage $devisImage): static
    {
        if (!$this->devisImages->contains($devisImage)) {
            $this->devisImages->add($devisImage);
            $devisImage->setDevis($this);
        }

        return $this;
    }

    public function removeDevisImage(DevisImage $devisImage): static
    {
        if ($this->devisImages->removeElement($devisImage)) {
            // set the owning side to null (unless already changed)
            if ($devisImage->getDevis() === $this) {
                $devisImage->setDevis(null);
            }
        }

        return $this;
    }

    public function getMetier(): ?Activity
    {
        return $this->metier;
    }

    public function setMetier(?Activity $metier): static
    {
        $this->metier = $metier;

        return $this;
    }

    /**
     * @return Collection<int, DevisArtisan>
     */
    public function getDevisArtisans(): Collection
    {
        return $this->devisArtisans;
    }

    public function addDevisArtisan(DevisArtisan $devisArtisan): static
    {
        if (!$this->devisArtisans->contains($devisArtisan)) {
            $this->devisArtisans->add($devisArtisan);
            $devisArtisan->setDevis($this);
        }

        return $this;
    }

    public function removeDevisArtisan(DevisArtisan $devisArtisan): static
    {
        if ($this->devisArtisans->removeElement($devisArtisan)) {
            // set the owning side to null (unless already changed)
            if ($devisArtisan->getDevis() === $this) {
                $devisArtisan->setDevis(null);
            }
        }

        return $this;
    }
}
