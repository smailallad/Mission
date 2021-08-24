<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserInscriptionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
//    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username',TextType::class,array('label' => 'Nom'))
            ->add('nom',TextType::class)
            ->add('naissance',DateType::class,[
                'label' => 'date de naisssance',
            ])
            ->add('email',EmailType::class)
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe'),

            ])
            ->add('submit',SubmitType::class,array(
                'attr' => array('class' => 'btn btn-success pull-right'),
                'label'=> 'Valider'
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}