<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\User;
use App\Form\ActivityAutocompleteField;
use App\Repository\ServicesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
            ])
            ->add('lastName', TextType::class, [
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
                    'class' => 'col-6 col-md-6',
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Telephone',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-6',
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
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-6',
                ],
            ])
            ->add('siren', TextType::class, [
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-6',
                ],
            ])
            ->add('website', TextType::class, [
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-6',
                ],
            ])

            /* Adresse et localisation */
            ->add('address', TextType::class, [
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control artisan-input',
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-12',
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
            ])


            /* Présentation */
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control user_profile_textarea',
                ]
            ])
            
            /* Informations techniques */
            ->add('activity', ActivityAutocompleteField::class)

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
                    'class' => 'col-12',
                ],
                'choice_attr' => function () {
                    return [
                        'class' => 'form-check-input',
                    ];
                },
                'label_attr' => [
                    'class' => 'form-check-label',
                ],
            ])


            /* logo entreprise */
            ->add('imageFile', VichImageType::class, [
                'label' => 'Logo',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false, 
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
