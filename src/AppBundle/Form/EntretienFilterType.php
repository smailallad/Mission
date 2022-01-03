<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntretienFilterType extends AbstractType
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
                            {   return $v->createQueryBuilder('v')
                                        ->orderBy('v.nom');
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
                            {   return $u->createQueryBuilder('u')
                                        ->orderBy('u.nom');
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
            'data_class'        => 'AppBundle\Entity\EntretienVehicule',
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
        return 'entretien_filter';
    }


}
