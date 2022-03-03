<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/client")
 * @Security("has_role('ROLE_FACTURATION')")
 */
class ClientController extends Controller
{
    /**
     * @Route("/index",name="client_index")
     */
    public function indexAction()
    {
        $clients = $this->getDoctrine()->getRepository("AppBundle:Client")->findAll();
       
        return $this->render('@App/Client/index.html.twig', array(
            "clients"        => $clients
        ));
    }

    /**
     * @Route("/New",name="client_new")
     */
    public function NewAction()
    {
        return $this->render('AppBundle:Client:new.html.twig', array(
            // ...
        ));
    }

}
