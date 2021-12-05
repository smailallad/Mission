<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

class PrestationFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom',Filters\TextFilterType::class, array('label' => "Nom"))
        ->add('sousProjet',EntityType::Class, array(
            'label'         =>'Sous projet',
            'class'         => 'AppBundle:SousProjet',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir un sous projet-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $s)
                            {   return $s->createQueryBuilder('s');
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
            'data_class'        => 'AppBundle\Entity\Prestation',
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
        return 'prestation_filter';
    }


}
