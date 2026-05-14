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

namespace App\Form\Visiteurs\Devis;

use App\Entity\Activity;
use App\Entity\Devis;
use App\Entity\Enum\DebutChantierEnum;
use App\Repository\ActivityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class CreationDevisType extends AbstractType
{
    public function __construct(
        private ActivityRepository $activityRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'placeholder' => 'Rénovation cuisine 12m² avec îlot central',
                ],
            ])

            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Cuisine actuelle des années 90, à refaire entièrement. Souhait d\'un îlot central, plan de travail en pierre, hotte aspirante, four à hauteur. Sol et carrelage à refaire. Possibilité d\'abattre une cloison non porteuse.',
                ],
            ])

            ->add('surface', TextType::class, [
                'attr' => [
                    'placeholder' => '12 m²',
                ],
            ])

            ->add('budget', RangeType::class, [
                'attr' => [
                    'min' => 500,
                    'max' => 100000,
                    'value' => 55000,
                ],
            ])

            ->add('debutChantier', EnumType::class, [
                'class' => DebutChantierEnum::class,
                'expanded' => true,
                'multiple' => false,
                'data' => DebutChantierEnum::UN_MOIS,
                'choices' => [
                    'ASAP' => DebutChantierEnum::ASAP,
                    '< 1 mois' => DebutChantierEnum::UN_MOIS,
                    '< 3 mois' => DebutChantierEnum::TROIS_MOIS,
                ],
            ])

            ->add('photos', DropzoneType::class, [
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'multiple' => true,
                    'placeholder' => 'Ajouter jusqu\'a des 20 photos',
                    'class' => 'd-none',
                ],
            ])
        ;

        /*
         * Toutes les activités
         */
        $activities = $this->activityRepository
            ->createQueryBuilder('a')
            ->getQuery()
            ->getResult();

        /*
         * Mélange aléatoire
         */
        shuffle($activities);

        /*
         * 4 métiers aléatoires
         */
        $randomActivities = array_slice($activities, 0, 5);

        $builder

            ->add('metierPrincipal', EntityType::class, [
                'class' => Activity::class,
                'choices' => $randomActivities,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'placeholder' => false,
                'mapped' => false,
                'required' => false,
            ])

            ->add('autreMetier', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un métier',
                'required' => false,
                'mapped' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
