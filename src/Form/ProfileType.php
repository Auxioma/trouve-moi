<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ActivityAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control user_profile_form_control mb-3',
                    'readonly' => true,
                    'disable' => true,
                ]
            ])

            ->add('compagny', TextType::class, [
                'attr' => [
                    'class' => 'form-control user_profile_form_control mb-3',
                    'readonly' => true,
                    'disable' => true,
                ]
            ])

            ->add('activity', ActivityAutocompleteField::class, [
                'label' => 'Métier',
                'required' => false,
                'attr' => [
                    'class' => ' user_profile_form_select mb-3'
                ]
            ])

            ->add('address', TextType::class, [
                'attr' => [
                    'class' => 'form-control user_profile_form_control mb-3',
                    'readonly' => true,
                    'disable' => true,
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control user_profile_form_control mb-3',
                    'readonly' => true,
                    'disable' => true,
                ]
            ])

            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control user_profile_textarea',
                ]
            ])
            
            ->add('website', TextType::class, [
                'attr' => [
                    'class' => 'user_profile_info_input',
                ]
            ])
            ->remove('siren')
            ->remove('postalCode')
            ->remove('city')
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
