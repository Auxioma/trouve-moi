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
} 