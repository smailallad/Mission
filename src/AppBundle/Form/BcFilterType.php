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

class BcFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('num',Filters\TextFilterType::class, array('label' => "Num"))
        ->add('date',DateRangeFilterType::Class, array(
            'label' =>'Date BC',
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
        ->add('responsableBc',EntityType::Class, array(
            'label'         => 'Responsable',
            'class'         => 'AppBundle:ResponsableBc',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir un responsable-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $r)
                            {   return $r->createQueryBuilder('r');
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
            'data_class'        => 'AppBundle\Entity\Bc',
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
        return 'bs_filter';
    }


}
