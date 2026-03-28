<?php

namespace App\Form;

use App\Dto\QuoteRequestDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteRequestType extends AbstractType
{
    private function requiredLabel(string $label): string
    {
        return $label . ' <span class="text-danger">*</span>';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => $this->requiredLabel('Nom'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('firstName', TextType::class, [
                'label' => $this->requiredLabel('Prénom'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prénom',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('address', TextType::class, [
                'label' => $this->requiredLabel('Adresse postale'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Adresse postale',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('postalCity', TextType::class, [
                'mapped' => false,
                'label' => $this->requiredLabel('Code postal / Ville'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Code postal / Ville',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('phone', TelType::class, [
                'label' => $this->requiredLabel('Téléphone'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Téléphone',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => $this->requiredLabel('Email'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-0',
                ],
            ])

            ->add('projectType', ChoiceType::class, [
                'label' => $this->requiredLabel('Type de projet'),
                'label_html' => true,
                'required' => true,
                'choices' => [
                    'Rénovation de salle de bain' => 'Rénovation de salle de bain',
                    'Plomberie' => 'Plomberie',
                    'Électricité' => 'Électricité',
                    'Peinture' => 'Peinture',
                    'Menuiserie' => 'Menuiserie',
                    'Maçonnerie' => 'Maçonnerie',
                    'Chauffage' => 'Chauffage',
                ],
                'attr' => [
                    'class' => 'form-select selection-devis mb-3',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => '',
                ],
                'placeholder' => 'Sélectionnez un type de projet',
            ])

            ->add('artisanType', ChoiceType::class, [
                'label' => $this->requiredLabel('Artisan recherché'),
                'label_html' => true,
                'required' => true,
                'choices' => [
                    'Plombier' => 'Plombier',
                    'Électricien' => 'Électricien',
                    'Peintre' => 'Peintre',
                    'Menuisier' => 'Menuisier',
                    'Maçon' => 'Maçon',
                    'Chauffagiste' => 'Chauffagiste',
                ],
                'placeholder' => 'Sélectionnez',
                'attr' => [
                    'class' => 'form-select selection-devis',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'col-12 mb-3',
                ],
            ])

            ->add('desiredDelay', ChoiceType::class, [
                'label' => $this->requiredLabel('Délai souhaité'),
                'label_html' => true,
                'required' => true,
                'choices' => [
                    'Moins de 48h' => 'Moins de 48h',
                    'Dans la semaine' => 'Dans la semaine',
                    'Dans le mois' => 'Dans le mois',
                    'Flexible' => 'Flexible',
                ],
                'placeholder' => 'Choisissez un délai',
                'attr' => [
                    'class' => 'form-select selection-devis',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'col-12 mb-3',
                ],
            ])

            ->add('urgence', ChoiceType::class, [
                'label' => $this->requiredLabel('Urgence du projet'),
                'label_html' => true,
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Urgent' => 'urgent',
                    'Dans la semaine' => 'semaine',
                    'Dans le mois' => 'mois',
                    'Flexible' => 'flexible',
                ],
                'label_attr' => [
                    'class' => 'form-label d-block',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('budget', RangeType::class, [
                'label' => $this->requiredLabel('Budget estimé'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'min' => 500,
                    'max' => 50000,
                    'step' => 100,
                    'class' => 'form-range mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('message', TextareaType::class, [
                'label' => $this->requiredLabel('Décrivez votre besoin'),
                'label_html' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control devis-textarea',
                    'placeholder' => "Décrivez votre besoin (surface, problème, urgence, contraintes d'accès, matériaux souhaités...)",
                    'rows' => 4,
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('photos', FileType::class, [
                'label' => 'Ajoutez des photos',
                'label_html' => true,
                'required' => false,
                'multiple' => true,
                'mapped' => true,
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control',
                ],
                'help' => 'Les demandes avec photos reçoivent généralement plus de réponses et permettent aux professionnels de mieux comprendre votre projet.',
                'label_attr' => [
                    'class' => 'fw-bold mb-2 text-primary-custom',
                ],
                'row_attr' => [
                    'class' => 'upload-box mb-4',
                ],
            ])

            ->add('contactPhone', CheckboxType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'row_attr' => [
                    'class' => 'mb-2',
                ],
            ])

            ->add('contactEmail', CheckboxType::class, [
                'label' => 'Email',
                'required' => false,
                'row_attr' => [
                    'class' => 'mb-2',
                ],
            ])

            ->add('contactSms', CheckboxType::class, [
                'label' => 'SMS',
                'required' => false,
                'row_attr' => [
                    'class' => 'mb-2',
                ],
            ])

            ->add('contactChat', CheckboxType::class, [
                'label' => 'Chat',
                'required' => false,
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('callbackAvailability', ChoiceType::class, [
                'label' => $this->requiredLabel('Disponibilité pour être rappelé'),
                'label_html' => true,
                'required' => true,
                'choices' => [
                    '9h00 - 12h00, 14h00 - 18h00' => '9h00 - 12h00, 14h00 - 18h00',
                    '8h00 - 12h00' => '8h00 - 12h00',
                    '12h00 - 14h00' => '12h00 - 14h00',
                    '14h00 - 18h00' => '14h00 - 18h00',
                    '18h00 - 20h00' => '18h00 - 20h00',
                ],
                'placeholder' => 'Choisir une disponibilité',
                'attr' => [
                    'class' => 'form-select selection-devis',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('interventionMoment', ChoiceType::class, [
                'label' => $this->requiredLabel('Moment préféré pour l’intervention'),
                'label_html' => true,
                'required' => true,
                'choices' => [
                    'En semaine' => 'En semaine',
                    'Le matin' => 'Le matin',
                    'L’après-midi' => 'L’après-midi',
                    'Le soir' => 'Le soir',
                    'Le week-end' => 'Le week-end',
                ],
                'placeholder' => 'Choisir un moment',
                'attr' => [
                    'class' => 'form-select selection-devis',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Recevoir mes devis gratuits',
                'attr' => [
                    'class' => 'btn btn-success btn-lg px-5 py-3 fw-bold devis-submit-btn',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuoteRequestDto::class,
        ]);
    }
}