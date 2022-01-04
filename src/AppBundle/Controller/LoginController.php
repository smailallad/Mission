<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{   /**
    * @Route("/login", name="login")
    */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $errors =$authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUserName();
        
        //$manager = $this->getDoctrine()->getManager();
        //$menu_famille = $manager->getRepository('AppBundle:Famille')->childrenHierarchy();
        //$promos = $manager->getRepository('AppBundle:Article')->getPromo();

        return $this->render('@App/Login/login.html.twig', array(
            'errors'            => $errors,
            'username'          => $lastUserName,
        ));

    }

    /**
    * @Route("/register", name="register")
    */
    public function registerAction(Request $request)
    {
        $passwordEncoder = $this->get('security.password_encoder');
        $manager = $this->getDoctrine()->getManager();
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username',TextType::class,array('label' => 'Nom'))
            ->add('email',EmailType::class)
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe'),
                ])
            ->add('submit',SubmitType::class,array(
                'attr' => array('class' => 'btn btn-success pull-right'),
                'label'=> 'Valider'
                ))
            ->getForm()
            ;

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setEnabled(false);

         	$groupes = $manager->getRepository('AppBundle:Groupes')->find(1);

         	$user->setGroups($groupes);

            if ($form->isValid())
            {
                $manager->persist($user);
                $manager->flush();
                return $this->redirectToRoute('login');
            }


        }
        
        return $this->render('@App/Register/register.html.twig', array(
            'form' => $form->createView()
        ));
    }


}
