<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
class EmployeEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::Class,array(
                'label' => 'Nom et prénom',
                )
            )
            ->add('username',TextType::Class,array(
                        'label' => 'UserId',
                        //'disabled'=> true
                        )
                    )
           ->add('email',EmailType::Class)
/*            ->add('password',RepeatedType::class,[
                        'type' => PasswordType::class,
                        'first_options' => array('label' => 'Mot de passe'),
                        'second_options' => array('label' => 'Confirmer le mot de passe'),
                        ])*/
            ->add('naissance',DateType::Class, array(
                        'label' =>'Date de naissance',
                        'widget' => 'single_text',
                        'html5' => true,
                        //'attr' => ['class' => 'js-datepicker']
                        )
                    )
//            ->add('isActive',null,array('label' => 'Utilisateur Active :'))
			->add('active',  ChoiceType::Class, array(
                        'choices'       => array(
                            'Non Active'=> 0,
                            'Active'    => 1
                        ),
                        'required'      => true,
                        'placeholder'   => '-Choisir-',
                        'label'         => 'Active',
                        'empty_data'    => null
                        )
                    )
            ->add('mission',  ChoiceType::Class, array(
                        'choices'       => array(
                            'Non Missionnaire'=> 0,
                            'Missionnaire'    => 1
                        ),
                        'required'      => true,
                        'placeholder'   => '-Choisir-',
                        'label'         => 'Missionnaire',
                        'empty_data'    => null
                        )
                    )
/*            ->add('groupes', EntityType::Class, array(
                        'class'         => 'AppBundle:Groupes',
                        'choice_name'   => 'groupname',
                        'multiple'      => false,
                        'placeholder'   => '-Choisir un Groupe-',
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                                        {   return $er->createQueryBuilder('g');
                                        },
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
            //'data_class' => null,
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'employe';
    }
}
