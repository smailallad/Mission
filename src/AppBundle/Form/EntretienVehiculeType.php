<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EntretienVehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateType::Class, array(
                'label'     => 'Date',
                'widget'    => 'single_text',
                'html5'     => true,
            ))
            
            ->add('vehicule',EntityType::class, array(
                'class'         => 'AppBundle:Vehicule',
                'label'         => 'Vehicule',
                'choice_name'   => 'nom',
                'multiple'      => false,
                'placeholder'   => '-Choisir-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $v)
                                {   return $v   ->createQueryBuilder('v')
                                                ->where('v.active=1')
                                            ;
                                },
                    )
                )
            ->add('user',EntityType::class, array(
                'class'         => 'AppBundle:User',
                'label'         => 'Chauffeur',
                'choice_name'   => 'nom',
                'multiple'      => false,
                'placeholder'   => '-Choisir-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $u)
                                {   return $u   ->createQueryBuilder('u')
                                                ->where('u.active=1')
                                            ;
                                },
                            )
                )
            ->add('kms',NumberType::class,array(
                'label'     => 'Kms',
                )
            )
            ->add('obs',TextareaType::class,array(
                'required'      => false,
                )
            )
        ;
    }/**
     * {@inheritdoc}
     * 
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\EntretienVehicule'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_entretienvehicule';
    }


}