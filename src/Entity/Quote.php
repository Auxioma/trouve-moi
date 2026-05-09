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

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    public const TYPE_PDF_UPLOADED = 'pdf_uploaded';
    public const TYPE_MANUAL = 'manual';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_OPENED = 'opened';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_PAID = 'paid';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $artisan = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $clientUser = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 50)]
    private string $type = self::TYPE_MANUAL;

    #[ORM\Column(length: 50)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $clientName = null;

    #[ORM\Column(length: 255)]
    private ?string $clientEmail = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $clientPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientAddress = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $quoteDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validUntil = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $subtotalHt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $tvaRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $tvaAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $totalTtc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $executionTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentTerms = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $legalNotes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isPdfUploaded = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isPdfGenerated = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $openedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $acceptedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    private Collection $items;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteFile::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC', 'id' => 'DESC'])]
    private Collection $files;

    #[ORM\ManyToOne(inversedBy: 'quotes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Conversation $conversation = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->quoteDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClientUser(): ?User
    {
        return $this->clientUser;
    }

    public function setClientUser(?User $clientUser): static
    {
        $this->clientUser = $clientUser;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): static
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): static
    {
        $this->clientPhone = $clientPhone;

        return $this;
    }

    public function getClientAddress(): ?string
    {
        return $this->clientAddress;
    }

    public function setClientAddress(?string $clientAddress): static
    {
        $this->clientAddress = $clientAddress;

        return $this;
    }

    public function getQuoteDate(): ?\DateTimeImmutable
    {
        return $this->quoteDate;
    }

    public function setQuoteDate(\DateTimeImmutable $quoteDate): static
    {
        $this->quoteDate = $quoteDate;

        return $this;
    }

    public function getValidUntil(): ?\DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTimeImmutable $validUntil): static
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function getSubtotalHt(): ?string
    {
        return $this->subtotalHt;
    }

    public function setSubtotalHt(?string $subtotalHt): static
    {
        $this->subtotalHt = $subtotalHt;

        return $this;
    }

    public function getTvaRate(): ?string
    {
        return $this->tvaRate;
    }

    public function setTvaRate(?string $tvaRate): static
    {
        $this->tvaRate = $tvaRate;

        return $this;
    }

    public function getTvaAmount(): ?string
    {
        return $this->tvaAmount;
    }

    public function setTvaAmount(?string $tvaAmount): static
    {
        $this->tvaAmount = $tvaAmount;

        return $this;
    }

    public function getTotalTtc(): ?string
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(?string $totalTtc): static
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    public function getExecutionTime(): ?string
    {
        return $this->executionTime;
    }

    public function setExecutionTime(?string $executionTime): static
    {
        $this->executionTime = $executionTime;

        return $this;
    }

    public function getPaymentTerms(): ?string
    {
        return $this->paymentTerms;
    }

    public function setPaymentTerms(?string $paymentTerms): static
    {
        $this->paymentTerms = $paymentTerms;

        return $this;
    }

    public function getLegalNotes(): ?string
    {
        return $this->legalNotes;
    }

    public function setLegalNotes(?string $legalNotes): static
    {
        $this->legalNotes = $legalNotes;

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

    public function isPdfUploaded(): bool
    {
        return $this->isPdfUploaded;
    }

    public function setIsPdfUploaded(bool $isPdfUploaded): static
    {
        $this->isPdfUploaded = $isPdfUploaded;

        return $this;
    }

    public function isPdfGenerated(): bool
    {
        return $this->isPdfGenerated;
    }

    public function setIsPdfGenerated(bool $isPdfGenerated): static
    {
        $this->isPdfGenerated = $isPdfGenerated;

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getOpenedAt(): ?\DateTimeImmutable
    {
        return $this->openedAt;
    }

    public function setOpenedAt(?\DateTimeImmutable $openedAt): static
    {
        $this->openedAt = $openedAt;

        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeImmutable $acceptedAt): static
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): static
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    /**
     * @return Collection<int, QuoteItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(QuoteItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setQuote($this);
        }

        return $this;
    }

    public function removeItem(QuoteItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getQuote() === $this) {
                $item->setQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuoteFile>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(QuoteFile $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setQuote($this);
        }

        return $this;
    }

    public function removeFile(QuoteFile $file): static
    {
        if ($this->files->removeElement($file)) {
            if ($file->getQuote() === $this) {
                $file->setQuote(null);
            }
        }

        return $this;
    }

    public function markAsSent(): static
    {
        $this->status = self::STATUS_SENT;
        $this->sentAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function markAsOpened(): static
    {
        $this->status = self::STATUS_OPENED;
        $this->openedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function markAsAccepted(): static
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->acceptedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function markAsPaid(): static
    {
        $this->status = self::STATUS_PAID;
        $this->paidAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }
}
