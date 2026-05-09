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

namespace App\Form\Authentification\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Votre prénom',
                    'autofocus' => true,
                    'autocomplete' => 'given-name',
                    'spellcheck' => 'false',
                    'class' => 'tm-signup-account-input form-control',
                    'maxlength' => 100,
                    'minlength' => 2,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre prénom.',
                    ),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Votre prénom ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^[\p{L}\s\'-]+$/u',
                        message: 'Le prénom contient des caractères invalides.',
                    ),
                ],
            ])

            ->add('lastName', TextType::class, [
                'label' => false,
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Votre nom',
                    'autocomplete' => 'family-name',
                    'spellcheck' => 'false',
                    'class' => 'tm-signup-account-input form-control',
                    'maxlength' => 100,
                    'minlength' => 2,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre nom.',
                    ),
                    new Length(
                        min: 2,
                        max: 100,
                        minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Votre nom ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^[\p{L}\s\'-]+$/u',
                        message: 'Le nom contient des caractères invalides.',
                    ),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Votre adresse email',
                    'autocomplete' => 'email',
                    'spellcheck' => 'false',
                    'class' => 'tm-signup-account-input form-control',
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre adresse email.',
                    ),
                    new Length(
                        max: 180,
                        maxMessage: 'L’adresse email ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Email(
                        message: 'Veuillez saisir une adresse email valide.',
                    ),
                ],
            ])

            ->add('phoneNumber', TelType::class, [
                'label' => false,
                'required' => true,
                'trim' => true,
                'attr' => [
                    'placeholder' => 'Votre numéro de téléphone',
                    'autocomplete' => 'tel',
                    'spellcheck' => 'false',
                    'class' => 'tm-signup-account-input form-control',
                    'maxlength' => 20,
                    'minlength' => 6,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir votre numéro de téléphone.',
                    ),
                    new Length(
                        min: 6,
                        max: 20,
                        minMessage: 'Le numéro de téléphone doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^[0-9+\s().-]+$/',
                        message: 'Veuillez saisir un numéro de téléphone valide.',
                    ),
                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                'label' => false,
                'required' => true,
                'trim' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Votre mot de passe',
                    'autocomplete' => 'new-password',
                    'class' => 'tm-signup-account-input form-control',
                    'maxlength' => 255,
                    'minlength' => 8,
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir un mot de passe.',
                    ),
                    new Length(
                        min: 12,
                        max: 255,
                        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
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
