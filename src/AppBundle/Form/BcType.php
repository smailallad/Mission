<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BcType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('num',TextType::class, array(
            'label'         => 'Num',
            'attr'          => array(
                //'class'     => 'text-uppercase'
                )
            )
        )
        ->add('date',DateType::Class, array(
            'label'     => 'Date',
            'widget'    => 'single_text',
            'html5'     => true,
            )
        )
        ->add('responsableBc',EntityType::class, array(
            'label'         => 'Responsable',
            'class'         => 'AppBundle:responsableBc',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir un responsable-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $r)
                            {   return $r->createQueryBuilder('r');
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
            'data_class'        => 'AppBundle\Entity\Bc',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bc';
    }
}
