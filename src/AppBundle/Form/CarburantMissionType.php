<?php
namespace AppBundle\Form;
use AppBundle\Entity\Vehicule;
use AppBundle\Entity\Carburant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CarburantMissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
                ->add('date',DateType::Class, array(
                    'label'     => 'Dateeee',
                    'widget'    => 'single_text',
                    'html5'     => true,
                    )
                )
                ->add('vehicule', EntityType::Class, array(
                    'class'         => 'AppBundle:Vehicule',
                    'choice_name'   => 'nom',
                    'label'         => 'Vehicule',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir le carburant-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $v)
                                    {   return $v   ->createQueryBuilder('v')
                                                    ->where('v.active = 1')
                                                    ->orderBy('v.nom');
                                    },
                    )
                )
                ->add('carburant', EntityType::Class, array(
                    'class'         => 'AppBundle:Carburant',
                    'choice_name'   => 'nom',
                    'label'         => 'Carburant',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir le carburant-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                                    {   return $c->createQueryBuilder('c')
                                                 ->orderBy('c.nom');
                                    },
                    )
                )
                ->add('justificationDepense',EntityType::Class,array(
                    'class'         => 'AppBundle:JustificationDepense',
                    'choice_name'   => 'nom',
                    'label'         => 'justification',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir une justification-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $j)
                                    {   return $j->createQueryBuilder('j');
                                    },
                    )
                )
                ->add('montant',NumberType::Class)
                ->add('kms',NumberType::Class)
                ->add('obs',TextareaType::Class,array(
                    'required'      => false,
                ))
                ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'AppBundle\Entity\CarburantMission',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_carburantMission';
    }
}
