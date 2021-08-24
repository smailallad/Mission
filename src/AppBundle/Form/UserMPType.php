<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;

//use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;


class UserMPType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $constraintsOptions = array(
            'message' => 'Mot de passe invalide',
        );

        $builder
            ->add('password', PasswordType::class, array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'valleur null non accepter')
                    ), 
                    /*new Length(array(
                        'min' => 3, 
                        'minMessage' => 'Name is too short'
                    ))*/
                ), 
                'label' => 'Name',
                'attr'   => array(
                    'readonly'  => true,
                    //'class'     => 'form-control'
                    )
                )    
            )    

            /*->add('password', PasswordType::class, array(
            'label' => 'Nouveau mot de passe',
            'constraints' => array(
                new NotBlank(),'constraints' => array(
                new NotBlank(),
                new UserPassword($constraintsOptions),
                ),
            ),
            'attr'   => array(
                    'readonly'  => true,
                    //'class'     => 'form-control'
                    )
                )
            )*/
            /*->add('current_password', PasswordType::class, array(
            'label' => 'Mot de passe actuel',
            'mapped' => false,
            'constraints' => array(
                new NotBlank(),
                new UserPassword($constraintsOptions),
            ),
            'attr' => array(
                'autocomplete' => 'current-password',
            ),
            ))*/
            /*->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe'),
                'invalid_message' => 'Mot de passe non conforme',

            ])*/
            ->add('submit',SubmitType::class,array(
                'attr' => array('class' => 'btn btn-success pull-right'),
                'label'=> 'Valider'
                )
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}