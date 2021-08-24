<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RecrutementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('recruter',DateType::Class, array(
            'label' =>'Date de recrutement',
            'widget' => 'single_text',
            'html5' => true,
            //'attr' => ['class' => 'js-datepicker']
            )
        )
        ->add('fonction', EntityType::Class, array(
            'mapped'        => false,
            'label'         =>'Fonction',
            'required'      => true,
            'class'         => 'AppBundle:Fonction',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir une Fonction-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                            {   return $er->createQueryBuilder('f');
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
            'data_class' => 'AppBundle\Entity\Recrutement'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_recrutement';
    }
}
