<?php
namespace AppBundle\Form;
use AppBundle\Repository\SiteRepository;
use Symfony\Component\Form\AbstractType;
use AppBundle\Repository\PrestationRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
<<<<<<< HEAD
            'choice_name'   => 'nom',
            'multiple'      => false,
            //'required'      => true,
            //'attr' => array('size' => '20'),
            'query_builder' => function(PrestationRepository $er) use($projet)
                                    {
                                       return $er->getPrestationsProjet($projet);
=======
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
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
                                    },
                                    //'attr' =>array('class'=>'form-control')
            )
        ) 
        ->add('zone',EntityType::class, array(
            'label'         => 'Zone',
            'class'         => 'AppBundle:Zone',
            'choice_name'   => 'nom',
            'multiple'      => false,
            //'required'      => true,
            'attr' => array(
                'onchange' => 'siteDelete()',
            )
            )
        )
        ->add('siteId',HiddenType::class,array(
            'mapped'        => false,
        ))
        ->add('siteCode',TextType::class,array(
            'label'         => 'Code site',
            'mapped'        => false,
            'attr'          => array(
                'readonly'  => true,
                'class'     => 'form-control'
            )
        ))
        ->add('siteNom',TextType::class,array(
            'label'         => 'Nom site',
            'mapped'        => false,
            'attr'          => array(
                'readonly'  => true,
                'class'     => 'form-control'
            )
        ))

        ->add('unite',TextType::class,array(
            'label'         => 'Unité'
            )

        )
        ->add('quantite',NumberType::class, array(
            'label'         => 'Quantité',
            )
        )
        ->add('montant',NumberType::class, array(
            'label'         => 'Montant',
            //'required'      => false,
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
<<<<<<< HEAD
            'projet'            => null,
=======
            'projet'        => null,
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
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
