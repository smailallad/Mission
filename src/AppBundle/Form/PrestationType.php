<?php
namespace AppBundle\Form;
use AppBundle\Entity\SousProjet;
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
        ->add('sousProjet',EntityType::class, array(
            'label'         => 'Sous projet',
            'class'         => 'AppBundle:SousProjet',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir un sous projet-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $s)
                            {   return $s->createQueryBuilder('s');
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
