<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class VehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom',TextType::class, array(
            'label'         => 'Nom',
            'attr'          => array(
                'class'     => 'text-uppercase'
                )
            )
        )
        ->add('matricule',TextType::class, array(
            'label'         => 'Matricule',
            )
        )
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
        ->add('active',ChoiceType::class, array(
            'label'         => 'Active',
            'choices'       => array(
                "Active"   => "1",
                'Non active'   => "0",
                )
            )
        )
        
        ->add('nbrjAlertRelever',NumberType::class, array(
            'label'         =>'Alert en jours'
            )
        )

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Vehicule',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vehicule';
    }
}
