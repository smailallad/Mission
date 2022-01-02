<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType;

class PointageFilterType extends AbstractType
{
    /**
     * {@inheritdoc} 
     */
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder
        /*->add('date',DateType::Class, array(
            'label'     => 'Au',
            'widget'    => 'single_text',
            'html5'     => true,
            'required'  => true,
            'data' => new \DateTime("now"),
            )
        )*/
        ->add('date',DateRangeFilterType::Class, array(
            'label' =>'Pointage',
            'left_date_options' => [
                'label'     =>'Du',
                'widget'    => 'single_text',
                //'format'  => \IntlDateFormatter::SHORT,
                //'attr'    => ['class' => 'date-picker form-filter'],
                'data'    => (new \DateTime()),
                'html5'     => true,
            ],
            'right_date_options' => [
                'label'     => 'Au',
                'widget'    => 'single_text',
                //'format'  => \IntlDateFormatter::SHORT,
                //'attr'    => ['class' => 'date-picker form-filter'],
                'data'    => (new \DateTime()),
                'html5'     => true,
            ],
            )
        )
        ->add('user',EntityType::Class, array(
                    'label'         =>'Employé',
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
