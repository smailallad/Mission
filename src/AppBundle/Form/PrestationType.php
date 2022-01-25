<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PrestationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom',TextType::class, array(
            'label'         => 'Nom',
            )
        )
        ->add('qte',NumberType::class, array(
            'label'         => 'qte'
            )
        )
        ->add('projet',EntityType::class, array(
            'label'         => 'Projet',
            'class'         => 'AppBundle:Projet',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir un projet-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $p)
                            {   return $p->createQueryBuilder('p')
                                            ->orderBy('p.nom')
                                            ;
                            },
            )
        )
        ->add('bc',EntityType::class, array(
            'label'         => 'Bon de commande',
            'class'         => 'AppBundle:Bc',
            'choice_name'   => 'num',
            'multiple'      => false,
            'placeholder'   => '-Choisir un BC-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $bc)
                            {   return $bc->createQueryBuilder('bc')
                                            ->orderBy('bc.num')
                                            ;
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
            'data_class'        => 'AppBundle\Entity\Prestation',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'prestation';
    }
}
