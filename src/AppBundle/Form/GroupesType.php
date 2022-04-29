<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupesType extends AbstractType
{
    /**
     * {@inheritdoc} 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('groupname',null,array('label' => 'Nom du groupe'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Groupes',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'groupes';
    }
}
