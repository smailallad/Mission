<?php

namespace AppBundle\Form;

use AppBundle\Entity\SousProjet;
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
        ->add('sous_projet',EntityType::class,array(
            'class'         => 'AppBundle:SousProjet',
            'label'         => 'Projet',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $s)
                                {
                                    return $s   ->createQueryBuilder('s')
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
