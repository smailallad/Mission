<?php
namespace AppBundle\Form;

use AppBundle\Entity\User;

//use Symfony\Component\Form\Extension\Core\Type\EmailType;
use AppBundle\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;


 
class ResetPasswordType extends AbstractType {
 
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('oldPassword', PasswordType::class,array(
                    'label' => 'Mot de passe actuel'
                ))
                ->add('Password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
                    'invalid_message' => 'Mot de passe non conforme',
                    'options' => array(
                        'attr' => array(
                            'class' => 'password-field'
                        )
                    ),
                    'required' => true,
                ))
                ->add('submit',SubmitType::class,array(
                'attr' => array('class' => 'btn btn-success pull-right'),
                'label'=> 'Valider'
            ));
 
        ;
    }
 
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            //mettre le nouveau formulaire
            'data_class' => ChangePassword::class,
            'csrf_token_id' => 'change_password',
        ));
    }
 
}