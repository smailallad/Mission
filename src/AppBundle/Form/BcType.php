<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            'class'         => 'AppBundle:ResponsableBc',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir un responsable-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $r)
                            {   return $r->createQueryBuilder('r');
                            },
            )
        )
        ->add('projet',EntityType::class, array(
            'label'         => 'Projet',
            'class'         => 'AppBundle:Projet',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir un PO-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $p)
                            {   return $p->createQueryBuilder('p')
                                ->orderBy('p.nom')
                                ;
                            },
            )
        )
        ->add('po',EntityType::class, array(
            'label'         => 'Po',
            'class'         => 'AppBundle:Po',
            'choice_name'   => 'num',
            'multiple'      => false,
            'required'      => false,
            'placeholder'   => '-Choisir un projet-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $p)
                            {   return $p->createQueryBuilder('p');
                            },
            )
        )
        ->add('otp',TextType::class, array(
            'label'         => 'OTP'    
            )
        )
        ->add('active')
        ->add('description',TextType::class, array(
            'label'         => 'Description'    
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
