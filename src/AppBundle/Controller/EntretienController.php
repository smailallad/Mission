<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/entretion")
 * @Security("has_role('ROLE_VEHICULE')")
 */
class EntretienController extends Controller
{
    /**
     * @Route("/index",name="entretien_index")
     */
    public function indexAction()
    {
        return $this->render('@App/Entretien/index.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/new",name="entretien_new")
     */
    public function newAction()
    {
        return $this->render('@App/Entretien/new.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/edit",name="entretien_edit")
     */
    public function editAction()
    {
        return $this->render('@App/Entretien/edit.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/delete",name="entretien_delete")
     */
    public function deleteAction()
    {
        return $this->render('@App/Entretien/delete.html.twig', array(
            // ...
        ));
    }

}
