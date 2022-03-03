<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

class SiteFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('code',Filters\TextFilterType::class, array('label' => "Code"))
        ->add('nom',Filters\TextFilterType::class, array('label' => "Nom"))
        ->add('nouveau', ChoiceType::Class, array(
                    'choices'       => array(
                        'Nouveau'   => 1,
                        'Valider'   => 0
                    ),
                    'required'      => false,
                    'placeholder'   => '-Tous-',
                    'label'         => 'Nouveau site',
                    'empty_data'    => null
                    )
                )
        ->add('wilaya',EntityType::Class, array(
            'label'         =>'Wilaya',
            'class'         => 'AppBundle:Wilaya',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir une wilaya-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $w)
                            {   return $w->createQueryBuilder('w');
                            },
            )
        )
        ->add('client',EntityType::Class, array(
            'label'         =>'Client',
            'class'         => 'AppBundle:Client',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir un client-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                            {   return $c->createQueryBuilder('c');
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
            'data_class'        => 'AppBundle\Entity\Site',
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
        return 'site_filter';
    }


}
