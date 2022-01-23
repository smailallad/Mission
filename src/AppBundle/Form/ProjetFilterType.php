<?php

namespace AppBundle\Form;

use AppBundle\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

class ProjetFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom',Filters\TextFilterType::class, array(
            'label' => "Nom"
            )
        )
        ->add('client',EntityType::class, array(
            'label'         => 'Client',
            'class'         => 'AppBundle:Client',
            'choice_name'   => 'nom',
            'multiple'      => false,
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
            'data_class'        => 'AppBundle\Entity\Projet',
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
        return 'Projet_filter';
    }


}
