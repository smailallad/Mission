<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\InterventionVehiculeRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class InterventionEntretienType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entretien      = $options['entretien'];
        $intervention   = $options['intervention'];
        //dump($intervention);
        $builder
        ->add('interventionVehicule',EntityType::class, array(
            'label'         => 'Intervention',
            'class'         => 'AppBundle:InterventionVehicule',
            'choice_name'   => 'designation',
            'multiple'      => false,
            'placeholder'   => '-Choisir-',
            /*'query_builder' => function(\Doctrine\ORM\EntityRepository $i)
                            {   return $i->createQueryBuilder('i');
                            },*/
            'query_builder' => function(InterventionVehiculeRepository $q) use($entretien,$intervention)
                            {
                               return $q->getNotInterventionEntretien($entretien,$intervention);
                            },
            )
        )
        ->add('qte',NumberType::class, array(
            'label'         => 'Quantité',
            'attr'  => array(
                 'placeholder'    => 'Quantité',
                )
            )
        )
        ->add('obs',TextType::class, array(
            'label'         => 'Obs',
            'required'      => false,
            'attr'  => array(
                'placeholder'    => 'Observation',
               )
           )
        )
        ->add('id_old',HiddenType::class,array(
            'mapped'    => false,
            'data'      => null,
        ))

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'AppBundle\Entity\InterventionEntretien',
            'method'        => 'POST',
            'entretien'     => null,
            'intervention'  => null,
            'attr'          => array(
                                'id' => 'form_add'
                                )
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form_intervention_entretien';
    }
}
