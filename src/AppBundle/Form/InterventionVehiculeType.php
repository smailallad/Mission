<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
