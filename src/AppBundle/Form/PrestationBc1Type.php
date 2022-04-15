<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PrestationBc1Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
            ->add('quantite',NumberType::class, array(
                'label'         => 'Quantité',
                )
            )
            ->add('montant',NumberType::class, array(
                'label'         => 'Montant',
                //'required'      => false,
                )
            )
            ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\PrestationBc',
            'method'            => 'POST',
            
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'new_prestation_bc1';
    }
    
}
