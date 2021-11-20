<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VehiculeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('marque',EntityType::Class, array(
            'label'         =>'Marque',
            'class'         => 'AppBundle:Marque',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir une marque-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $m)
                            {   return $m->createQueryBuilder('m');
                            },
            )
        )
        ->add('active', ChoiceType::Class, array(
            'choices'       => array(
                'Active'       => 1,
                'Non active'   => 0
            ),
            'required'      => false,
            'placeholder'   => '-Tous-',
            'label'         => 'Active',
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
            'data_class'        => 'AppBundle\Entity\Vehicule',
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
        return 'vehicule_filter';
    }


}
