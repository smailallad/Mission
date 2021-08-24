<?php
namespace AppBundle\Form;
use AppBundle\Entity\TarifPrestation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class TarifPrestationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('montant',NumberType::class, array(
            'label'         => 'Montant',
            )
        )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\TarifPrestation',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tarif_prestation';
    }
    
}
