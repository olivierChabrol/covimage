<?php

namespace App\Form;

use App\Entity\Params;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ParamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['entity_manager'];
        $params = $entityManager->getRepository(Params::class)->findBy(['type_param' => Params::PARAM]);
        $tabParam = [];
        foreach($params as $param)
        {
            $tabParam [$param->getValue()] = $param->getId();
        }
        $builder
            ->add('type_param', ChoiceType::class, [
                'choices' => $tabParam
            ])
            ->add('value')
            ->add('label')
            ->add('color')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Params::class,
        ]);
        $resolver->setRequired('entity_manager');
    }
}
