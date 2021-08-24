<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DepenseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom',TextType::class, array(
            'label'         => 'Nom',
            'attr'          => array(
                "class"     => "text-capitalize",
                //'readonly'  => true,
                )
            )
        )
        ->add('FamilleDepense',EntityType::class, array(
            'label'         => 'Famille',
            'class'         => 'AppBundle:FamilleDepense',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'placeholder'   => '-Choisir une famille-',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $f)
                            {   return $f->createQueryBuilder('f');
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
            'data_class'        => 'AppBundle\Entity\Depense',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'depense';
    }
}
