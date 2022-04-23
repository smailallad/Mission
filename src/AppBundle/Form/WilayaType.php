<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class WilayaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('code',TextType::class, array(
            'label'         => 'Code',
            )
        )
        ->add('nom',TextType::class, array(
            'label'         => 'Nom'
            )
        )
        ->add('zone',EntityType::class, array(
            'label'         => 'Zone',
            'class'         => 'AppBundle:Zone',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir une zone-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $z)
                            {   return $z->createQueryBuilder('z');
                            },
            )
        )
        ->add('montantFm',NumberType::class,array(
            'label'         =>'Montat frais de misson',
            )
        )

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Wilaya',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wilaya';
    }
}
