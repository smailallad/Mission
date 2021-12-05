<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class KmsInterventionVehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('marque',EntityType::class, array(
            'label'         => 'Marque',
            'class'         => 'AppBundle:Marque',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir une marque-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $m)
                            {   return $m->createQueryBuilder('m');
                            },
            )
        )
        ->add('kms',TextType::class, array(
            'label'         => 'Kms'
            )
        )
        ->add('obs',TextType::class, array(
            'label'         => 'Obs',
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
