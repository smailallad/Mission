<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FraisMissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $builder
                ->add('dateFm',DateType::Class, array(
                    'label'     => 'Date',
                    'widget'    => 'single_text',
                    'html5'     => true,
                    )
                )
                ->add('user', EntityType::Class, array(
                    'class'         => 'AppBundle:User',
                    'choice_name'   => 'nom',
                    'label'         => 'Employe',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir une employe-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $e)
                                    {   return $e->createQueryBuilder('e')
                                                ->where('e.active = true')
                                                ->andWhere('e.mission = true')
                                                ->orderBy('e.nom');
                                    },
                    )
                )
                ->add('montant',NumberType::Class)
                ->add('obs',TextareaType::Class,array(
                    'required'      => false,
                ))
                ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'AppBundle\Entity\FraisMission',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_fraisMission';
    }
}
