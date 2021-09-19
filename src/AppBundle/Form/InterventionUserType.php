<?php
namespace  AppBundle\Form;

use AppBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionUserType extends AbstractType {
  
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id     = $options['id'];
        $date   = $options['date'];
        $mission= $options['mission'];
        $builder
        ->add('User',EntityType::class, array(
            'label'         => 'Realisateur',
            'class'         => 'AppBundle:User',
            'choice_name'   => 'nom',
            'multiple'      => true,
            'attr' => array('size' => '20'),
            'query_builder' => function(UserRepository $er) use($id,$date,$mission)
                                    {
                                       return $er->getNotRealisateursIntervention($id,$date,$mission);
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
            'data_class' => 'AppBundle\Entity\InterventionUser',
            'id'         => null,
            'date'       => null,
            'mission'    => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_InterventionUser';
    }


}
