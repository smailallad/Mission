<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
class AvanceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        /*->add('code',TextType::class, array(
                'label'         => 'Code',
                'attr'          => array(
                    'class'     => 'text-uppercase',
                    'disabled'  => true
                )
            )
        )
        ->add('depart',DateType::Class, array(
                'label'     => 'Date de départ',
                'widget'    => 'single_text',
                'html5'     => true,
                'attr'      => array(
                    'disabled'  =>true
                )
            )
        )
        ->add('retour',DateType::Class, array(
                'label'     => 'Date de retour',
                'widget'    => 'single_text',
                'html5'     => true,
                'attr'      => array(
                    'disabled'  =>true
                )
            )
        )
        ->add('user',EntityType::class, array(
                'class'         => 'AppBundle:User',
                'label'         => 'Chef de mission',
                //'choice_name'   => 'nom',
                'attr'      => array(
                    'disabled'  =>true
                )
            )
        )*/
        ->add('avance',NumberType::class)
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AppBundle\Entity\Mission',
            'method'            => 'POST',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'avance';
    }
}
