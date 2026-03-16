<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre Email <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Votre Email'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.',
                    ),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe <span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Mot de passe',
                    'autocomplete' => 'new-password'
                ], 'constraints' => [ 
                        new NotBlank( message: 'Please enter a password', ), 
                        new Length( min: 6, minMessage: 'Your password should be at least {{ limit }} characters')
                    ],           
                ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Numéro de téléphone <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Numéro de téléphone'
                ]
            ])
            ->add('siret', TextType::class, [
                'label' => 'Numéro SIREN <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'mapped' => false,
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : 552100554',
                    'maxlength' => 9
                ]
            ])
            ->add('siren', HiddenType::class)
            ->add('compagny', TextType::class, [
                'label' => 'Nom de l’entreprise',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
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
