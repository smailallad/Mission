<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Groupes;

use AppBundle\Form\UserInscriptionType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegisterController extends Controller
{
    /**
    * @Route("/register",name="register")
    */
    public function registerAction(Request $request)
    {
        $passwordEncoder = $this->get('security.password_encoder');
        $manager = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(UserInscriptionType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setEnabled(false);
            //$manager = $this->getDoctrine()->getManager();
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
