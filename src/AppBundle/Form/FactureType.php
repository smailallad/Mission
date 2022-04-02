<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FactureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $vbc      = $options['vbc'];
        $builder
            ->add('num')
            ->add('date',DateType::Class, array(
                'label'     => 'Date',
                'widget'    => 'single_text',
                'html5'     => true,
                )
            )
            ->add('tva')
            ->add('bc',EntityType::class,array(
                'label'         => 'BC',
                'class'         => 'AppBundle:Bc',
                'choice_name'   => 'num',
                'multiple'      => false,
                'placeholder'   => '-Choisir un BC-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $bc) use ($vbc)
                                {   $q = $bc ->createQueryBuilder('bc')
                                        ->where('bc.active = true')
                                    ;
                                    if ($vbc != null)
                                    {
                                        $q = $q ->orWhere('bc = :vbc')
                                                ->setParameter('vbc',$vbc)
                                        ;
                                    }
                                    return $q;
                                },
                )
            );
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'AppBundle\Entity\Facture',
            'vbc'           => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_facture';
    }


}
