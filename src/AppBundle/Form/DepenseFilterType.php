<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;

class DepenseFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom',Filters\TextFilterType::class, array('label' => "Nom"))
        ->add('nouveau', ChoiceType::Class, array(
                    'choices'       => array(
                        'Nouveau'       => 1,
                        'Valider'   => 0
                    ),
                    'required'      => false,
                    'placeholder'   => '-Tous-',
                    'label'         => 'Nouveau',
                    'empty_data'    => null
                    )
                )
        ->add('familleDepense',EntityType::Class, array(
            'label'         =>'Famille',
            'class'         => 'AppBundle:FamilleDepense',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir une famille-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $w)
                            {   return $w->createQueryBuilder('w');
                            },
            )
        )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Depense',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'depense_filter';
    }


}
