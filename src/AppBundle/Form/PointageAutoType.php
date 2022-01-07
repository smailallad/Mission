<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PointageAutoType extends AbstractType
{
    /**
     * {@inheritdoc} 
     */
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder
        ->add('date',DateType::Class, array(
            'label'     => 'Date',
            'widget'    => 'single_text',
            'html5'     => true,
            'required'  => true,
            'data' => new \DateTime("now"),
            )
        )

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class'        => 'AppBundle\Entity\PointageUser',
            'data_class'        => null,
            'csrf_protection'   => false,
            //'validation_groups' => array('filter'),
            'method'            => 'GET',
            'attr'          => array(
                'id' => 'form_pointage_auto'
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pointage_auto';
    }


}
