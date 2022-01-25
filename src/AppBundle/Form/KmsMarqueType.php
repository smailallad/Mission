<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class KmsMarqueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('interventionVehicule',EntityType::class, array(
            'label'         => 'Intevention',
            'class'         => 'AppBundle:interventionVehicule',
            'choice_name'   => 'designation',
            'multiple'      => false,
            'placeholder'   => '-Choisir une intervention-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $i)
                            {   return $i->createQueryBuilder('i');
                            },
            )
        )
        ->add('kms',NumberType::class, array(
            'label'         => 'Kms'
            )
        )
        ->add('obs',TextType::class, array(
            'label'         => 'Obs',
            'required'      => false
            )
        )
        
        
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\KmsInterventionVehicule',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kmsInterventionVehicule';
    }
}
