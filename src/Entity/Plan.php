<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null; // Gratuit, Pro, Premium

    #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null; // free, pro, premium

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $priceMonthly = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $priceYearly = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $features = [];

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getPriceMonthly(): ?string { return $this->priceMonthly; }
    public function setPriceMonthly(string $priceMonthly): static { $this->priceMonthly = $priceMonthly; return $this; }

    public function getPriceYearly(): ?string { return $this->priceYearly; }
    public function setPriceYearly(?string $priceYearly): static { $this->priceYearly = $priceYearly; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getFeatures(): ?array { return $this->features; }
    public function setFeatures(?array $features): static { $this->features = $features; return $this; }
}
