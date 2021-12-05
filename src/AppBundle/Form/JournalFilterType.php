<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType;

class JournalFilterType extends AbstractType
{
    /**
     * {@inheritdoc} 
     */
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder
        ->add('mission',Filters\TextFilterType::class, array('label' => "Mission"))
        ->add('code',Filters\TextFilterType::class, array('label' => "Code site"))
        ->add('site',Filters\TextFilterType::class, array('label' => "Nom site"))
        ->add('periode',DateRangeFilterType::Class, array(
                    'label' =>'Période',
                    'left_date_options' => [
                        'label'     =>'Du',
                        'widget'    => 'single_text',
                        'html5'     => true,
                    ],
                    'right_date_options' => [
                        'label'     => 'Au',
                        'widget'    => 'single_text',
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

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class'        => 'AppBundle\Entity\Intervention',
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
        return 'journal_filter';
    }


}
