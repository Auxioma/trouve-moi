<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\User;
use App\Repository\ServicesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use App\Form\ActivityAutocompleteField;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;

class ProfileType extends AbstractType
{
    public function __construct(
        private readonly ServicesRepository $servicesRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['data'] ?? null;
        $activity = $user?->getActivity();

        $builder
            /* Informations personnelles */
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'readonly' => true,
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire.'),
                    new Length(
                        max: 100,
                        maxMessage: 'Le prénom ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'readonly' => true,
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire.'),
                    new Length(
                        max: 100,
                        maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'readonly' => true,
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'constraints' => [
                    new NotBlank(message: 'L’adresse email est obligatoire.'),
                    new Email(message: 'Veuillez saisir une adresse email valide.'),
                ],
            ])

            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => '06 12 34 56 78',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'required' => false,
                'constraints' => [
                    new Length(
                        max: 20,
                        maxMessage: 'Le numéro de téléphone ne doit pas dépasser {{ limit }} caractères.'
                    ),
                    new Regex(
                        pattern: '/^[0-9+\s().-]*$/',
                        message: 'Le numéro de téléphone contient des caractères invalides.'
                    ),
                ],
            ])

            /* Informations entreprise */
            ->add('compagny', TextType::class, [
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => 'Nom de votre entreprise',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'required' => false,
                'constraints' => [
                    new Length(
                        max: 255,
                        maxMessage: 'Le nom de l’entreprise ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('siren', TextType::class, [
                'label' => 'SIREN',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => '123456789',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'required' => false,
                'constraints' => [
                    new Regex(
                        pattern: '/^\d{9}$/',
                        message: 'Le numéro SIREN doit contenir exactement 9 chiffres.'
                    ),
                ],
            ])

            ->add('website', TextType::class, [
                'label' => 'Site internet',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => 'https://www.mon-site.fr',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'required' => false,
                'constraints' => [
                    new Url(message: 'Veuillez saisir une URL valide.'),
                ],
            ])

            /* Adresse et localisation */
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => '12 rue Exemple',
                ],
                'row_attr' => [
                    'class' => 'col-12',
                ],
                'required' => false,
                'constraints' => [
                    new Length(
                        max: 255,
                        maxMessage: 'L’adresse ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => '75000',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'required' => false,
                'constraints' => [
                    new Regex(
                        pattern: '/^\d{5}$/',
                        message: 'Le code postal doit contenir exactement 5 chiffres.'
                    ),
                ],
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                    'placeholder' => 'Paris',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'required' => false,
                'constraints' => [
                    new Length(
                        max: 120,
                        maxMessage: 'Le nom de la ville ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            /* Présentation */
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control user_profile_textarea',
                    'rows' => 6,
                    'placeholder' => 'Présentez votre activité, vos services et votre expérience...',
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12',
                ],
                'constraints' => [
                    new Length(
                        max: 2000,
                        maxMessage: 'La description ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            /* Informations techniques */
            ->add('activity', ActivityAutocompleteField::class, [
                'label' => 'Métier',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner un métier.'),
                ],
            ])

            ->add('services', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choices' => $activity
                    ? $this->servicesRepository->findBy(['activity' => $activity], ['name' => 'ASC'])
                    : [],
                'row_attr' => [
                    'class' => 'col-6 d-none',
                ],
                'choice_attr' => static fn () => [
                    'class' => 'form-check-input',
                ],
                'label_attr' => [
                    'class' => 'form-label',
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