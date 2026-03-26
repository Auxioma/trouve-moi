<?php

namespace App\Form;

use App\Dto\QuoteRequestDto;
use App\Entity\Activity;
use App\Entity\Services;
use App\Repository\ServicesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class QuoteRequestType extends AbstractType
{
    public function __construct(
        private readonly ServicesRepository $servicesRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuoteRequestDto|null $data */
        $data = $options['data'] ?? null;
        $activity = $data?->activity;

        // =========================
        // ETAPE 1
        // =========================
        $builder

            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un métier',
                'required' => false,
                'disabled' => true,

                'row_attr' => [
                    'class' => 'step step-1 col-12',
                ],

                'label_attr' => [
                    'class' => 'form-label w-100',
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('requestType', ChoiceType::class, [
                'label' => 'Type de besoin',
                'required' => false,
                'placeholder' => 'Sélectionnez',

                'choices' => [
                    'Installation' => 'installation',
                    'Réparation' => 'reparation',
                    'Rénovation' => 'renovation',
                    'Entretien' => 'entretien',
                    'Dépannage' => 'depannage',
                    'Autre' => 'autre',
                ],

                'row_attr' => [
                    'class' => 'step step-2 col-6',
                ],

                'label_attr' => [
                    'class' => 'form-label',
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Décrivez votre besoin',
                'required' => false,

                'row_attr' => [
                    'class' => 'step step-2 col-12',
                ],

                'label_attr' => [
                    'class' => 'form-label',
                ],

                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Expliquez votre projet, vos besoins, les travaux à réaliser...',
                ],
            ])

            ->add('urgency', ChoiceType::class, [
                'label' => 'Niveau d’urgence',
                'required' => false,
                'placeholder' => 'Sélectionnez un délai',

                'choices' => [
                    'Pas urgent' => 'not_urgent',
                    'Dans les 15 jours' => 'within_15_days',
                    'Le plus vite possible' => 'asap',
                ],

                'row_attr' => [
                    'class' => 'step step-2 col-6',
                ],

                'label_attr' => [
                    'class' => 'form-label',
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-3 col-12',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Numéro et rue',
                ],
            ])

            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-3 col-4',
                ],
                'label_attr' => [
                    'class' => 'form-label fw-semibold',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '75000',
                ],
            ])

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-3 col-md-8',
                ],
                'label_attr' => [
                    'class' => 'form-label fw-semibold',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Paris',
                ],
            ])

            ->add('accessDetails', TextareaType::class, [
                'label' => 'Informations d’accès',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-3 col-12',
                ],
                'label_attr' => [
                    'class' => 'form-label fw-semibold',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 1,
                    'placeholder' => 'Digicode, étage, ascenseur, parking, etc.',
                ],
            ])

            ->add('desiredDelay', ChoiceType::class, [
                'label' => 'Délai souhaité',
                'required' => false,
                'placeholder' => 'Sélectionnez',

                'choices' => [
                    'Dès que possible' => 'asap',
                    'Sous 24h' => '24h',
                    'Cette semaine' => 'week',
                    'Ce mois-ci' => 'month',
                    'Flexible' => 'flexible',
                ],

                'row_attr' => [
                    'class' => 'step step-4 col-6',
                ],

                'label_attr' => [
                    'class' => 'form-label',
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('budget', ChoiceType::class, [
                'label' => 'Budget',
                'required' => false,
                'placeholder' => 'Sélectionnez',

                'choices' => [
                    'Moins de 100 €' => 'lt_100',
                    '100 à 500 €' => '100_500',
                    '500 à 1 000 €' => '500_1000',
                    '1 000 à 5 000 €' => '1000_5000',
                    'Plus de 5 000 €' => 'gt_5000',
                    'À discuter' => 'unknown',
                ],

                'row_attr' => [
                    'class' => 'step step-4 col-6',
                ],

                'label_attr' => [
                    'class' => 'form-label',
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-5 col-6',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Jean',
                ],
            ])

            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-5 col-6',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Dupont',
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-5 col-6',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'email@exemple.fr',
                ],
            ])

            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'row_attr' => [
                    'class' => 'step step-5 col-6',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '06 00 00 00 00',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteRequestDto::class,
            'step' => 1,
            'validation_groups' => static function (FormInterface $form) {
                $step = $form->getConfig()->getOption('step');

                if ($step === 5) {
                    return false;
                }

                return ['Default', 'step'.$step];
            },
        ]);

        $resolver->setAllowedTypes('step', 'int');
    }
}