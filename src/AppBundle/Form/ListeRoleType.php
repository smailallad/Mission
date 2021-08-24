<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ListeRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('rolename',  EntityType::class, array(
                    'class' => 'AppBundle:Roles',
                    'choice_name' => 'rolename',
                    'multiple' => false,
                    'placeholder' => '-Choisir un role-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                                    {   return $er->createQueryBuilder('r');
                                    },
                     )
                  )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Roles',
        ));
    }


    public function getName()
    {
        return 'liste_role';
    }
}
