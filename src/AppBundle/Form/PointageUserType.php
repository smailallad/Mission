<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PointageUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateType::Class, array(
                'label'     => 'Date',
                'widget'    => 'single_text',
                'html5'     => true,
            ))
            ->add('user',EntityType::class,array(
                'class'         => 'AppBundle:User',
                'label'         => 'Employe',
                'placeholder'   => '-Choisir-',
                'multiple'      => true,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $u)
                                    {
                                        return $u   ->createQueryBuilder('u')
                                                    ->where('u.active = :v1')
                                                    ->andWhere('u.mission = :v2')
                                                    ->setParameter('v1',true)
                                                    ->setParameter('v2',true)
                                        ;
                                    }
            ))
            ->add('pointage')
            ->add('hTravail', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 12
                    ]]
                )
            ->add('hRoute',IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 12
                    ]]
                    )
            ->add('hSup',IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 12
                    ]]
                    )
            ->add('obs',TextareaType::Class)
            
            ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PointageUser'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_pointageuser';
    }


}
