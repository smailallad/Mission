<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
class NoteFraisType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('annee',ChoiceType::class,array(
                'choices'       => array(
                            date('Y')-1   => date('Y')-1,
                            date('Y')     => date('Y')
                        ),
                        'data'      => date('Y'),
                        'required'  => true,
                        'label'     => 'Année',
                        'mapped'    => false
            )
        )
        ->add('code',TextType::class, array(
                'label'         => 'Code',
                'attr'          => array(
                    'class'     => 'text-uppercase',
                    'readonly'  => true
                )
            )
        )
        ->add('depart',DateType::Class, array(
                'label'     => 'Du',
                'widget'    => 'single_text',
                'html5'     => true,
            )
        )
        ->add('retour',DateType::Class, array(
                'label'     => 'Au',
                'widget'    => 'single_text',
                'html5'     => true,
            )
        )
        ->add('user',EntityType::class, array(
                'class'         => 'AppBundle:User',
                'label'         => "L'interessé",
                'choice_name'   => 'nom',
                'multiple'      => false,
                'placeholder'   => '-Choisir un employé-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $u)
                                {   return $u   ->createQueryBuilder('u')
                                                ->where('u.active=1')
                                            ;
                                },
            )
        )
//        ->add('avance',NumberType::class)
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Mission',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'noteFrais';
    }
}
