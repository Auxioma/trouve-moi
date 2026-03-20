<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ActivityAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Activity::class,
            'choice_label' => 'name',
            'label' => 'Métier',
            'required' => false,
            'placeholder' => 'Choisissez une activité',
            'searchable_fields' => ['name'],
            'tom_select_options' => [
                'create' => false,
                'maxOptions' => 10,
            ],
            'attr' => [
                'class' => 'artisan-select',
            ],
            'row_attr' => [
                'class' => 'col-12 col-md-12',
            ],
            'label_attr' => [
                'class' => 'form-label',
            ],
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}