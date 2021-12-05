<?php

namespace AppBundle\Form;

use AppBundle\Entity\FamilleDepense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DepenseRechercheType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('FamilleDepense',EntityType::class,array(
            'class'         => 'AppBundle:FamilleDepense',
            'label'         => 'Famille',
            //'placeholder'   => '-Choisir un FamilleDepense-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $f)
                                {
                                    return $f   ->createQueryBuilder('f')
                                    ;
                                }
        ))
        ->add('nom',TextType::class, array(
            'label'         => "Depense",
            'required'      => false,
        ))
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Depense',
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
        //return 'Depense_filter';
        return 'Depense_recherche';
    }


}
