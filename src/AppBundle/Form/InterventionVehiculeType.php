<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InterventionVehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('designation',TextType::class, array(
            'label'         => 'Désignation'
            )
        )
        ->add('unite',TextType::class, array(
            'label'         => 'Unité',
            )
        )
        ->add('important',  ChoiceType::Class, array(
            'choices'       => array(
                'Non important'=> 0,
                'Important'    => 1
            ),
            'required'      => true,
            'placeholder'   => '-Choisir-',
            'label'         => 'Importance',
            'empty_data'    => null
            )
        )

        
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\InterventionVehicule',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'interventionVehicule';
    }
}
