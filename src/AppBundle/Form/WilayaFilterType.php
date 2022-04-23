<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

class WilayaFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('code',Filters\TextFilterType::class, array('label' => "Code"))
        ->add('nom',Filters\TextFilterType::class, array('label' => "Nom"))
        ->add('zone',EntityType::Class, array(
            'label'         =>'Zone',
            'class'         => 'AppBundle:Zone',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir une zone-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $z)
                            {   return $z->createQueryBuilder('z');
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
            'data_class'        => 'AppBundle\Entity\Zone',
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
        return 'zone_filter';
    }


}
