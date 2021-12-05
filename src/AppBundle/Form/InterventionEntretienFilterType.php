<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionEntretienFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('vehicule',EntityType::Class, array(
            'label'         =>'Vehicule',
            'class'         => 'AppBundle:Vehicule',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir un vehicule-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $v)
                            {   return $v->createQueryBuilder('v');
                            },
            )
        )
        ->add('user',EntityType::Class, array(
            'label'         => 'Chauffeur',
            'class'         => 'AppBundle:User',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir un chauffeur-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $u)
                            {   return $u->createQueryBuilder('u');
                            },
            )
        )
        ->add('interventionVehicule',EntityType::Class, array(
            'label'         => 'Intervention',
            'class'         => 'AppBundle:InterventionVehicule',
            'choice_name'   => 'designation',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir une intervention-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $i)
                            {   return $i->createQueryBuilder('i');
                            },
            )
        )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => null,
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
        return 'intevention_entretien_filter';
    }


}
