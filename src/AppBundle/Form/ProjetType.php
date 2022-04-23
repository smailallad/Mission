<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::Class,array(
                'label'     =>'Nom'
                )
            )
            ->add('client',EntityType::class, array(
                'label'         => 'Client',
                'class'         => 'AppBundle:Client',
                'choice_name'   => 'nom',
                'multiple'      => false,
                'placeholder'   => '-Choisir un client-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                                {   return $c->createQueryBuilder('c')
                                            ;
                                },
                )
            )
            ->add('clientFacturation',EntityType::class, array(
                'label'         => 'Client facturation',
                'class'         => 'AppBundle:ClientFacturation',
                'choice_name'   => 'nom',
                'multiple'      => false,
                'placeholder'   => '-Choisir un client-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                                {   return $c->createQueryBuilder('c')
                                            ;
                                },
                )
            )
            ->add('date',DateType::Class, array(
                'label' =>'Date creation projet',
                'widget' => 'single_text',
                'html5' => true,
                //'attr' => ['class' => 'js-datepicker']
                )
            )
            ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Projet'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_projet';
    }


}
