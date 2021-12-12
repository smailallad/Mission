<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
//use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

class InterventionVehiculeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('designation',Filters\TextFilterType::class, array(
            'label' => "Désignation"
            )
        )
        ->add('important', ChoiceType::Class, array(
            'choices'       => array(
                'Important'       => 1,
                'Nom important'   => 0
            ),
            'required'      => false,
            'placeholder'   => '-Tous-',
            'label'         => 'Importance',
            'empty_data'    => null
            )
        )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\InterventionVehicule',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'interventionVehicule_filter';
    }


}
