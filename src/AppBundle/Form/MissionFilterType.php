<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType;

class MissionFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder
        ->add('code',Filters\TextFilterType::class, array('label' => "Code"))
        ->add('depart',DateRangeFilterType::Class, array(
                    'label' =>'Date de Depart',
                    'left_date_options' => [
                        'label'     =>'Du',
                        'widget'    => 'single_text',
                        //'format'  => \IntlDateFormatter::SHORT,
                        //'attr'    => ['class' => 'date-picker form-filter'],
                        //'data'    => (new \DateTime()),
                        'html5'     => true,
                    ],
                    'right_date_options' => [
                        'label'     => 'Au',
                        'widget'    => 'single_text',
                        //'format'  => \IntlDateFormatter::SHORT,
                        //'attr'    => ['class' => 'date-picker form-filter'],
                        //'data'    => (new \DateTime('+1 day')),
                        'html5'     => true,
                    ],
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
        ->add('vEmploye', ChoiceType::Class, array(
                    'choices'       => array(
                        'Non validé'=> 0,
                        'Validé'    => 1
                    ),
                    'required'      => false,
                    'placeholder'   => '-Tous-',
                    'label'         => 'Validation Employe',
                    'empty_data'    => null
                    )
                )
       ->add('vRollout', ChoiceType::Class, array(
                    'choices'       => array(
                        'Non validé'=> 0,
                        'Validé'    => 1
                    ),
                    'required'      => false,
                    'placeholder'   => '-Tous-',
                    'label'         => 'Validation Rollout',
                    'empty_data'    => null
                    )
                )
        ->add('vComptabilite', ChoiceType::Class, array(
                    'choices'       => array(
                        'Non validé'=> 0,
                        'Validé'    => 1
                    ),
                    'required'      => false,
                    'placeholder'   => '-Tous-',
                    'label'         => 'Validation Comptabilite',
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
            'data_class'        => 'AppBundle\Entity\Mission',
            //'data_class'        => null,
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
        return 'mission_filter';
    }


}
