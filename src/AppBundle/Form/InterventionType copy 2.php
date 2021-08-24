<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'label'         => 'vehicule',
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
                    'label'     => 'Date de la fonction',
                    'widget'    => 'single_text',
                    'html5'     => true,
                    )
                )
            ->add('idsite',TextType::class,array(
                'label'         =>'idsite',
                'mapped'        =>false,
            ))
            ->add('site',TextType::class,array(
                'label'         => 'code site',
                'mapped'        => false,
                'attr'          => array(
                    'readonly'  => true,
                    'class'     => 'form-control'
                )
            ))
            ->add('nomsite',TextType::class,array(
                'label'         => 'nom site',
                'mapped'        => false,
                'attr'          => array(
                    'readonly'  => true,
                    'class'     => 'form-control'
                )
            ))
            ->add('idprestation',TextType::class,array(
                'mapped'        => false,
            ))
            ->add('sousprojet',TextType::class,array(
                'mapped'        => false,
                'label'         => 'equipement',
                'attr'          => array(
                    'readonly'  => true,
                    //'class'     => 'form-control'
                )
            ))
            ->add('prestation',TextType::class,array(
               'label'         => 'intervention',
                'mapped'        => false,
                'attr'          => array(
                    'readonly'  => true
                )
            ))
            ->add('quantite')
            ->add('designation')
            ;
    }
    /**
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
