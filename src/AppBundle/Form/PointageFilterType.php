<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PointageFilterType extends AbstractType
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
            )
        )
        ->add('user',EntityType::Class, array(
                    'label'         =>'Chef de mission',
                    'class'         => 'AppBundle:User',
                    'choice_name'   => 'nom',
                    'multiple'      => false,
                    'required'      => false,
                    'placeholder'   => '-Choisir-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $u)
                                    {  return $u->createQueryBuilder('u')
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
            //'data_class'        => 'AppBundle\Entity\PointageUser',
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
        return 'pointage_filter';
    }


}
