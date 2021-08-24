<?php
namespace AppBundle\Form;
use AppBundle\Entity\Zone;
use AppBundle\Entity\TarifPrestation;
use AppBundle\Repository\ZoneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class TarifPrestationNewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $prestation = $options['prestation'];
        //dump($prestation);
        $builder
        ->add('Zone',EntityType::class, array(
            'label'         => 'Zone',
            'class'         => 'AppBundle:Zone',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => true,
            //'attr' => array('size' => '20'),
            'query_builder' => function(ZoneRepository $er) use($prestation)
                                    {
                                       return $er->getAddZone($prestation);
                                    },
                                    //'attr' =>array('class'=>'form-control')
            )
        )
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
            'prestation'        => null,
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'new_tarif_prestation';
    }
    
}
