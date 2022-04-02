<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SiteRechercheType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('client',EntityType::class,array(
            'class'         => 'AppBundle:Client',
            'label'         => 'Client', 
            'attr' => array(
                'onchange' => 'fSiteChange()',
            ),
            //'placeholder'   => '-Choisir un client-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                                {
                                    return $c   ->createQueryBuilder('c')
                                    ;
                                }
        ))
        ->add('nom',TextType::class, array(
            'label'         => "Site",
            'required'      => false,
        ))
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
        //return 'site_filter';
        return 'site_recherche';
    }


}
