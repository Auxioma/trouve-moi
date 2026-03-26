<?php

namespace App\Dto;

use App\Entity\Activity;
use Symfony\Component\Validator\Constraints as Assert;

class QuoteRequestDto
{
    #[Assert\NotNull(message: 'Veuillez sélectionner un métier.', groups: ['step1'])]
    public ?Activity $activity = null;

    #[Assert\NotBlank(message: 'Veuillez sélectionner le type de besoin.', groups: ['step2'])]
    #[Assert\Length(max: 50, groups: ['step2'])]
    public ?string $requestType = null;

    #[Assert\NotBlank(message: 'Veuillez sélectionner un niveau d’urgence.', groups: ['step2'])]
    public ?string $urgency = null;

    #[Assert\NotBlank(message: 'Veuillez décrire votre besoin.', groups: ['step2'])]
    #[Assert\Length(
        min: 20,
        minMessage: 'Veuillez donner un peu plus de détails sur votre besoin.',
        max: 3000,
        groups: ['step2']
    )]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner l’adresse.', groups: ['step3'])]
    public ?string $address = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner le code postal.', groups: ['step3'])]
    public ?string $postalCode = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner la ville.', groups: ['step3'])]
    public ?string $city = null;

    public ?string $accessDetails = null;

    #[Assert\NotBlank(message: 'Veuillez sélectionner un délai souhaité.', groups: ['step3'])]
    public ?string $desiredDelay = null;

    public ?string $budget = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre prénom.', groups: ['step4'])]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre nom.', groups: ['step4'])]
    public ?string $lastName = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre email.', groups: ['step4'])]
    #[Assert\Email(message: 'Veuillez renseigner une adresse email valide.', groups: ['step4'])]
    public ?string $email = null;

    public ?string $phone = null;

}