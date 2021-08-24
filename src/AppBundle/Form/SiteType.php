<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('code',TextType::class, array(
            'label'         => 'Code',
            'attr'          => array(
                'class'     => 'text-uppercase'
                )
            )
        )
        ->add('nom',TextType::class, array(
            'label'         => 'Nom',
            'attr'          => array(
                "class"     => "text-capitalize",
                //'readonly'  => true,
                )
            )
        )
        ->add('wilaya',EntityType::class, array(
            'label'         => 'Wilaya',
            'class'         => 'AppBundle:Wilaya',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir une wilaya-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $w)
                            {   return $w->createQueryBuilder('w');
                            },
            )
        )
        ->add('client',EntityType::class, array(
            'label'         =>'Client',
            'class'         => 'AppBundle:Client',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir un client-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                            {   return $c->createQueryBuilder('c');
                            },
            )
        )
        ->add('nouveau',ChoiceType::class, array(
            'label'         => 'Nouveau',
            'choices'       => array(
                "Nouveau"   => "1",
                'Valider'   => "0",
                )
            )
        )

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Site',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site';
    }
}
