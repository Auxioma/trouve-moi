<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class QuoteRequestDto
{
    #[Assert\NotBlank(message: "Veuillez renseigner l’adresse.")]
    public ?string $address = null;

    #[Assert\NotBlank(message: "Veuillez renseigner le code postal.")]
    public ?string $postalCode = null;

    #[Assert\NotBlank(message: "Veuillez renseigner la ville.")]
    public ?string $city = null;

    #[Assert\NotBlank(message: "Veuillez renseigner votre prénom.")]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: "Veuillez renseigner votre nom.")]
    public ?string $lastName = null;

    #[Assert\Email(message: "Veuillez renseigner un email valide.")]
    public ?string $email = null;

    #[Assert\NotBlank(message: "Veuillez renseigner votre téléphone.")]
    public ?string $phone = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner un type de projet.")]
    public ?string $projectType = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner un artisan.")]
    public ?string $artisanType = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner un délai.")]
    public ?string $desiredDelay = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner l'urgence du projet.")]
    public ?string $urgence = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner un budget.")]
    #[Assert\Range(
        min: 500,
        max: 10000,
        notInRangeMessage: "Le budget doit être entre {{ min }}€ et {{ max }}€."
    )]
    public ?int $budget = null;

    #[Assert\NotBlank(message: 'Veuillez décrire votre besoin.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Votre message doit contenir au moins {{ limit }} caractères.',
        max: 2000,
        maxMessage: 'Votre message ne peut pas dépasser {{ limit }} caractères.'
    )]
    public ?string $message = null;

    #[Assert\All([
        new Assert\Image(
            maxSize: '8M',
            mimeTypesMessage: 'Veuillez importer une image valide (JPG, PNG, WEBP, etc.).',
            maxSizeMessage: 'Chaque image ne doit pas dépasser 8 Mo.'
        )
    ])]
    #[Assert\Count(
        max: 10,
        maxMessage: 'Vous pouvez ajouter մինչև {{ limit }} photos.'
    )]
    public ?array $photos = [];

    public ?bool $contactPhone = true;
    public ?bool $contactEmail = false;
    public ?bool $contactSms = false;
    public ?bool $contactChat = false;

    #[Assert\NotBlank(message: "Veuillez sélectionner votre disponibilité.")]
    public ?string $callbackAvailability = null;

    #[Assert\NotBlank(message: "Veuillez sélectionner le moment préféré pour l’intervention.")]
    public ?string $interventionMoment = null;

    // GETTERS & SETTERS

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $postalCode): self { $this->postalCode = $postalCode; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getProjectType(): ?string { return $this->projectType; }
    public function setProjectType(?string $projectType): self { $this->projectType = $projectType; return $this; }

    public function getArtisanType(): ?string { return $this->artisanType; }
    public function setArtisanType(?string $artisanType): self { $this->artisanType = $artisanType; return $this; }

    public function getDesiredDelay(): ?string { return $this->desiredDelay; }
    public function setDesiredDelay(?string $desiredDelay): self { $this->desiredDelay = $desiredDelay; return $this; }

    public function getUrgence(): ?string { return $this->urgence; }
    public function setUrgence(?string $urgence): self { $this->urgence = $urgence; return $this; }

    public function getBudget(): ?int { return $this->budget; }
    public function setBudget(?int $budget): self { $this->budget = $budget; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $message): self { $this->message = $message; return $this; }

    public function getPhotos(): ?array { return $this->photos; }
    public function setPhotos(?array $photos): self { $this->photos = $photos; return $this; }

    public function isContactPhone(): ?bool { return $this->contactPhone; }
    public function setContactPhone(?bool $contactPhone): self { $this->contactPhone = $contactPhone; return $this; }

    public function isContactEmail(): ?bool { return $this->contactEmail; }
    public function setContactEmail(?bool $contactEmail): self { $this->contactEmail = $contactEmail; return $this; }

    public function isContactSms(): ?bool { return $this->contactSms; }
    public function setContactSms(?bool $contactSms): self { $this->contactSms = $contactSms; return $this; }

    public function isContactChat(): ?bool { return $this->contactChat; }
    public function setContactChat(?bool $contactChat): self { $this->contactChat = $contactChat; return $this; }

    public function getCallbackAvailability(): ?string { return $this->callbackAvailability; }
    public function setCallbackAvailability(?string $callbackAvailability): self { $this->callbackAvailability = $callbackAvailability; return $this; }

    public function getInterventionMoment(): ?string { return $this->interventionMoment; }
    public function setInterventionMoment(?string $interventionMoment): self { $this->interventionMoment = $interventionMoment; return $this; }
}