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

namespace App\Form\Authentification\Artisan;

use App\Entity\Activity;
use App\Entity\User;
use App\Repository\ActivityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArtisanRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('compagny', TextType::class, [
                'required' => true,
                'attr' => [
                    'autocomplete' => 'organization',
                    'maxlength' => '255',
                    'spellcheck' => 'false',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner le nom de votre entreprise.',
                    ),
                    new Length(
                        min: 2,
                        max: 255,
                        minMessage: 'Le nom de l’entreprise doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le nom de l’entreprise ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('siret', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => '123 456 789 00012',
                    'autocomplete' => 'off',
                    'inputmode' => 'numeric',
                    'maxlength' => '14',
                    'spellcheck' => 'false',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner votre numéro SIRET.',
                    ),
                    new Length(
                        min: 14,
                        max: 14,
                        exactMessage: 'Le numéro SIRET doit contenir exactement {{ limit }} chiffres.',
                    ),
                    new Regex(
                        pattern: '/^[0-9]+$/',
                        message: 'Le numéro SIRET doit contenir uniquement des chiffres.',
                    ),
                ],
            ])

            ->add('lastName', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => 'Nom du gérant',
                    'autocomplete' => 'family-name',
                    'maxlength' => '100',
                    'spellcheck' => 'false',
                    'autocapitalize' => 'words',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner votre nom.',
                    ),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Votre nom ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^[\p{L}\s\'\-]+$/u',
                        message: 'Le nom contient des caractères non autorisés.',
                    ),
                ],
            ])

            ->add('phoneNumber', TelType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => '+33 6 12 34 56 78',
                    'autocomplete' => 'tel',
                    'inputmode' => 'tel',
                    'maxlength' => '20',
                    'spellcheck' => 'false',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner votre numéro de téléphone.',
                    ),
                    new Length(
                        min: 8,
                        max: 20,
                        minMessage: 'Le numéro de téléphone est trop court.',
                        maxMessage: 'Le numéro de téléphone est trop long.',
                    ),
                    new Regex(
                        pattern: '/^\+?[0-9\s\-\(\)\.]+$/',
                        message: 'Veuillez renseigner un numéro de téléphone valide.',
                    ),
                ],
            ])

            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => 'contact@entreprise.fr',
                    'autocomplete' => 'email',
                    'inputmode' => 'email',
                    'maxlength' => '180',
                    'spellcheck' => 'false',
                    'autocapitalize' => 'off',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner votre adresse email.',
                    ),
                    new Email(
                        message: 'Veuillez renseigner une adresse email valide.',
                        mode: Email::VALIDATION_MODE_STRICT,
                    ),
                    new Length(
                        max: 180,
                        maxMessage: 'L’adresse email ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un métier',
                'required' => true,
                'attr' => [
                    'class' => 'tm-signup-pro-select',
                ],
                'query_builder' => static fn (ActivityRepository $activityRepository) => $activityRepository
                    ->createQueryBuilder('a')
                    ->orderBy('a.name', 'ASC'),
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez sélectionner votre activité.',
                    ),
                ],
            ])

            ->add('city', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => 'Paris',
                    'autocomplete' => 'address-level2',
                    'maxlength' => '120',
                    'spellcheck' => 'false',
                    'autocapitalize' => 'words',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner votre ville.',
                    ),
                    new Length(
                        min: 2,
                        max: 120,
                        minMessage: 'Le nom de la ville doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le nom de la ville ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^[\p{L}\s\'\-\(\)]+$/u',
                        message: 'Le nom de la ville contient des caractères non autorisés.',
                    ),
                ],
            ])

            ->add('website', UrlType::class, [
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => 'https://www.mon-site.fr',
                    'autocomplete' => 'url',
                    'inputmode' => 'url',
                    'maxlength' => '255',
                    'spellcheck' => 'false',
                    'autocapitalize' => 'off',
                ],
                'constraints' => [
                    new Length(
                        max: 255,
                        maxMessage: 'Le site web ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Url(
                        message: 'Veuillez renseigner une URL valide.',
                        protocols: ['http', 'https'],
                    ),
                ],
            ])

            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-textarea',
                    'placeholder' => 'Présentez votre entreprise, vos spécialités, votre expérience et votre savoir-faire...',
                    'rows' => 6,
                    'maxlength' => '2000',
                    'spellcheck' => 'true',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner une description de votre activité.',
                    ),
                    new Length(
                        min: 50,
                        max: 2000,
                        minMessage: 'La description doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => false,
                'attr' => [
                    'class' => 'tm-signup-pro-file-input',
                    'accept' => '.jpg,.jpeg,.png,.webp',
                ],
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        maxSizeMessage: 'L’image ne doit pas dépasser {{ limit }}.',
                        mimeTypesMessage: 'Veuillez envoyer une image valide (JPG, PNG ou WEBP).',
                    ),
                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control tm-signup-pro-input',
                    'placeholder' => '••••••••••••',
                    'autocomplete' => 'new-password',
                    'minlength' => '12',
                    'maxlength' => '4096',
                    'spellcheck' => 'false',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez renseigner un mot de passe.',
                    ),
                    new Length(
                        min: 12,
                        max: 4096,
                        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+/',
                        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.',
                    ),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'J’accepte les conditions générales d’utilisation.',
                'label_html' => true,
                'attr' => [
                    'class' => 'tm-signup-account-checkbox',
                ],
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les conditions générales d’utilisation.',
                    ),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
