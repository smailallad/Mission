<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
//use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType;

//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EmployeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username', Filters\TextFilterType::class, array('label' => "Nom employé"))
            ->add('email', Filters\TextFilterType::class, array('label' => 'E-mail'))
//            ->add('active', Filters\BooleanFilterType::class, array('label' => 'Activé'))
			->add('active', ChoiceType::Class, array(
							'choices'       => array(
							    'Non active'=> 0,
								'Active'    => 1
							),
							'required'      => false,
							'placeholder'   => '-Choisir-',
							'label'         => 'Active',
							'empty_data'    => null
                        )
                    )
            ->add('mission', ChoiceType::Class, array(
                        'choices'       => array(
                            'Non missionnaire'=> 0,
                            'Missionnaire'    => 1
                        ),
                        'required'      => false,
                        'placeholder'   => '-Choisir-',
                        'label'         => 'Missionnaire',
                        'empty_data'    => null
                    )
                )

 //           ->add('groupes', Filters\EntityFilterType::class, array('class' => 'AppBundle\Entity\Groupes'))
            ->add('groupes', EntityType::Class, array(
                    'label'         =>'Groupe',
                    'class'         => 'AppBundle:Groupes',
                    'choice_name'   => 'groupname',
                    'multiple'      => false,
					'required'      => false,
                    'placeholder'   => '-Choisir un Groupe-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                                    {   return $er->createQueryBuilder('g');
                                    },
                    )
                )
           /* ->add('recrutement',DateRangeFilterType::Class, array(
                    'label' =>'Date de Recrutement',
                    //'widget' => 'single_text',
                    //'html5' => true,
                    //'attr' => ['class' => 'js-datepicker']
                    'left_date_options' => [
                        'label'     =>'Du',
                        'widget'    => 'single_text',
                        //'format'  => \IntlDateFormatter::SHORT,
                        //'attr'    => ['class' => 'date-picker form-filter'],
                        //'data'    => (new \DateTime()),
                        'html5'     => true,
                    ],
                    'right_date_options' => [
                        'label'     => 'Au',
                        'widget'    => 'single_text',
                        //'format'  => \IntlDateFormatter::SHORT,
                        //'attr'    => ['class' => 'date-picker form-filter'],
                        //'data'    => (new \DateTime('+1 day')),
                        'html5'     => true,
                    ],
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
            'data_class'        => 'AppBundle\Entity\User',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'employe_filter';
    }
}
