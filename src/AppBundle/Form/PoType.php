<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num')
            ->add('date',DateType::Class, array(
                'label'     => 'Date',
                'widget'    => 'single_text',
                'html5'     => true,
                )
            )
            ->add('active')
            /* ->add('projet',EntityType::class, array(
                'label'         => 'Projet',
                'class'         => 'AppBundle:Projet',
                'choice_name'   => 'nom',
                'multiple'      => false,
                'placeholder'   => '-Choisir un projet-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $p)
                                {   return $p->createQueryBuilder('p');
                                },
                )
            ) */
            ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Po'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_po';
    }


}
