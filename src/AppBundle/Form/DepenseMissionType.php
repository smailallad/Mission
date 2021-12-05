<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DepenseMissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
                ->add('dateDep',DateType::Class, array(
                    'label'     => 'Date',
                    'widget'    => 'single_text',
                    'html5'     => true,
                    )
                )
                ->add('depense', EntityType::Class, array(
                    'class'         => 'AppBundle:Depense',
                    'choice_name'   => 'nom',
                    'label'         => 'Dépense',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir une depense-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $d)
                                    {   return $d->createQueryBuilder('d')
                                                 ->orderBy('d.nom');
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
            'data_class'    => 'AppBundle\Entity\DepenseMission',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_depenseMission';
    }
}
