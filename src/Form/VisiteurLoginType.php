<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Services;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VisiteurLoginType extends AbstractType
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
                
                ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => [
                    'style' => 'margin: 20px 0;',
                ],
                    'row_attr' => [
                    'style' => 'margin: 0px 10px;',
                ],
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.  ',
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
