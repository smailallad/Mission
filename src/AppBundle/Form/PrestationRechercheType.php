<?php

namespace AppBundle\Form;

use AppBundle\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PrestationRechercheType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('projet',EntityType::class,array(
            'class'         => 'AppBundle:Projet',
            'label'         => 'Projet',
            'attr' => array(
                'onchange' => 'fPrestationChange()',
            ),
            'query_builder' => function(\Doctrine\ORM\EntityRepository $p)
                                {
                                    return $p   ->createQueryBuilder('p')
                                    ;
                                }
        ))
        ->add('nom',TextType::class, array(
            'label'         => "Intervention",
            'required'      => false,
        ))
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
