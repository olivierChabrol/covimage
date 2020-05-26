<?php

namespace App\Form;

use App\Entity\ImageUnit;
use App\Entity\ImageStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ImageStackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('name')
            ->add('quantity')
            ->add('images', CollectionType::class, [
                'entry_type' => ImageUnitType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ImageStack::class,
        ]);
    }
}
