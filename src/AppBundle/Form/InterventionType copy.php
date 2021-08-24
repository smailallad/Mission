<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class InterventionType extends AbstractType
{
    private $intervention;
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vehicule',EntityType::class,array(
                'class'         => 'AppBundle:Vehicule',
                'label'         => 'vehicule',
                'placeholder'   => '-Choisir un véhicule-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $v)
                                    {
                                        return $v   ->createQueryBuilder('v')
                                                    ->where('v.active = :v1')
                                                    ->setParameter('v1',true)
                                        ;
                                    }
            ))
            ->add('dateIntervention',DateType::Class, array(
                    'label'     => 'Date de la fonction',
                    'widget'    => 'single_text',
                    'html5'     => true,
                    )
                )
            ->add('client',EntityType::class,array(
                'class'         => 'AppBundle:Client',
                'choice_name'   => 'nom',
                'mapped'        => false,
                'multiple'      => false,
                'placeholder'   => '-Choisir un client-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $c)
                                    {  return  $c   ->createQueryBuilder('c')
                                                    ;
                                    },
            ))
            
            /*->add('prestation',EntityType::class,array(
                'class'         => 'AppBundle:prestation',
                'choice_name'   => 'nom',
                'mapped'        => false,
                'multiple'      => false,
                'placeholder'   => '-Choisir une prestattion-',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $p)
                                    {  return  $p   ->createQueryBuilder('p')
                                                    ;
                                    },
            ))*/
            ->add('quantite')
            ->add('designation')
            ;
            $builder->get('client')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event){
                    $form = $event->getForm();
                    $form->getparent()->add('site',EntityType::class,array(
                    'class'         => 'AppBundle:site',
                    'choice_name'   => 'nom',
                    'mapped'        => false,
                    'multiple'      => false,
                    'placeholder'   => '-Choisir un site-',
                    'choices'       => $form->getData()->getsites()
                    //'query_builder' => function(\Doctrine\ORM\EntityRepository $s)
                    //                    {  return  $s   ->createQueryBuilder('s')
                    //                                    ;
                    //                    },
                    )
                    );
                    //dump($event->getform()->getData());
                }
            );
/*
            $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                
                $form = $event->getForm();

                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                //dump($form);dump($data);
                $cli = $data->getClient();
                //dump($cli);

                $sport = $data->getSport();
                $positions = null === $sport ? [] : $sport->getAvailablePositions();

                $form->add('position', EntityType::class, [
                    'class' => 'App\Entity\Position',
                    'placeholder' => '',
                    'choices' => $positions,
                ]);
            }
        );*/
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'AppBundle\Entity\Intervention',
            
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_intervention';
    }


}
