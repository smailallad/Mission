<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VehiculeAssuranceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('debutAssurance',DateType::Class, array(
            'label'     => 'Date début',
            'widget'    => 'single_text',
            'html5'     => true,
            )
        )
        ->add('finAssurance',DateType::Class, array(
            'label'     => 'Date fin',
            'widget'    => 'single_text',
            'html5'     => true,
            )
        )
        ->add('obsAssurance',TextareaType::class,array(
            'label'     =>'Obs',
            'required'  => false
        ))

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Vehicule',
            //'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vehiculeAssurance';
    }
}
