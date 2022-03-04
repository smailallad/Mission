<?php
namespace AppBundle\Form;
use AppBundle\Repository\SiteRepository;
use Symfony\Component\Form\AbstractType;
use AppBundle\Repository\PrestationRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PrestationBcType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $projet = $options['projet'];
        $client = $options['client'];
        $builder
        ->add('prestation',EntityType::class, array(
            'label'         => 'Prestation',
            'class'         => 'AppBundle:Prestation',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => true,
            //'attr' => array('size' => '20'),
            'query_builder' => function(PrestationRepository $er) use($projet)
                                    {
                                       return $er->getPrestationsProjet($projet);
                                    },
                                    //'attr' =>array('class'=>'form-control')
            )
        )
        ->add('zone',EntityType::class, array(
            'label'         => 'Zone',
            'class'         => 'AppBundle:Zone',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => true,
            )
        )
        ->add('site',EntityType::class, array(
            'label'         => 'Site',
            'class'         => 'AppBundle:Site',
            'choice_name'   => 'nom',
            'multiple'      => false,
            'required'      => false,
            //'attr' => array('size' => '20'),
            'query_builder' => function(SiteRepository $er) use($client)
                                    {
                                       return $er->getSitesClient($client);
                                    },
                                    //'attr' =>array('class'=>'form-control')
            )
        )
        ->add('montant',NumberType::class, array(
            'label'         => 'Montant',
            )
        )
        ->add('qte',NumberType::class, array(
            'label'         => 'Quantité',
            )
        )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\PrestationBc',
            'method'            => 'POST',
            'projet'        => null,
            'client'            => null,
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'new_prestation_bc';
    }
    
}
