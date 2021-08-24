<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateFilterType;


class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username',TextType::Class,array('label' => 'Nom'))
            ->add('email',EmailType::Class)
/*            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confimer le mot de passe'),
                 ])
*/
//            ->add('isActive',null,array('label' => 'Utilisateur Active :'))

			->add('active',  ChoiceType::Class, array(
							'choices'       => array(
								'Non Active'=> 0,
							    'Active'    => 1
							),
							'required'      => true,
							'placeholder'   => '-Choisir-',
							'label'         => 'Utilisateur Active',
							'empty_data'    => null
                    ))

            ->add('groupes', EntityType::Class, array(
                    'class'         => 'AppBundle:Groupes',
                    'choice_name'   => 'groupname',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir un Groupe-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                                    {   return $er->createQueryBuilder('g');
                                    },
                     )
                  )
            /*->add('recrutement',DateFilterType::Class, array(
                    'label' =>'Date de Recrutement',
                    'widget' => 'single_text',
                    'html5' => true,
                    //'attr' => ['class' => 'js-datepicker']
                    )
                )*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}
