<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InterventionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('vehicule',EntityType::class,array(
                'class'         => 'AppBundle:Vehicule',
                'label'         => 'Vehicule',
                'placeholder'   => '-Choisir un véhicule-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $v)
                                    {
                                        return $v   ->createQueryBuilder('v')
                                                    ->where('v.active = :v1')
                                                    ->setParameter('v1',true)
                                        ;
                                    }
            ))
        ->add('dateIntervention',DateType::Class, array(
                'label'     => 'Date',
                'widget'    => 'single_text',
                'html5'     => true,
                )
            )
        ->add('sitecode',TextType::class,array(
                'label'         => 'Code site',
                'mapped'        => false,
                'attr'          => array(
                    'readonly'  => true,
                    'class'     => 'form-control'
                )
            ))
        ->add('sitenom',TextType::class,array(
                'label'         => 'Nom site',
                'mapped'        => false,
                'attr'          => array(
                    'readonly'  => true,
                    'class'     => 'form-control'
                )
            ))
        ->add('projet',TextType::class,array(
                'mapped'        => false,
                'label'         => 'Projet',
                'attr'          => array(
                    'readonly'  => true,
                    //'class'     => 'form-control'
                )
            ))
        ->add('prestationnom',TextType::class,array(
               'label'          => 'Intervention',
                'mapped'        => false,
                'attr'          => array(
                    'readonly'  => true
                ) 
            ))
        ->add('quantite',NumberType::class)
        ->add('designation',TextareaType::class,array(
                'attr'          => array(
                    'rows'      => 5,
                )
            ))
        ->add('reserves',TextareaType::class,array(
                'required'  => false,
                'attr'          => array(
                    'rows'      => 5,
                )
            ))

        ->add('prestationid',HiddenType::class,array(
                'mapped'        => false,
            ))
        ->add('siteid',HiddenType::class,array(
                'mapped'        => false,
            ))
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Intervention',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'intervention';
    }
}
