<?php
namespace AppBundle\Form;
use AppBundle\MyClass\DateKeyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class FonctionUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('datefonction',DateKeyType::Class, array(
                    'label'     => 'Date de la fonction',
                    'widget'    => 'single_text',
                    'html5'     => true,
                    )
                )
                ->add('fonction', EntityType::Class, array(
                    'class'         => 'AppBundle:Fonction',
                    'choice_name'   => 'fonction',
                    'multiple'      => false,
                    'placeholder'   => '-Choisir une fonction-',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $f)
                                    {   return $f->createQueryBuilder('f');
                                    },
                    )
                )
                ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\FonctionUser'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_fonctionUser';
    }
}
