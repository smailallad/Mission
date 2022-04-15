<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Po;
use AppBundle\Form\PoFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Po controller.
 *
 * @Route("po")
 * @Security("has_role('ROLE_FACTURATION')")
 */
class PoController extends Controller
{
    /**
     * Lists all po entities.
     *
     * @Route("/", name="po_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(PoFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'po', 'po_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Po')->createQueryBuilder('p');
        $paginator = $this->filter($form, $qb, 'po');
        $forme=$form->createView();
        return $this->render('@App/Po/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }

    /**
     * Creates a new po entity.
     *
     * @Route("/new", name="po_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $po = new Po();
        $form = $this->createForm('AppBundle\Form\PoType', $po);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($po);
            $em->flush();

            return $this->redirectToRoute('po_index');
        }

        return $this->render('@App/Po/new.html.twig', array(
            'po' => $po,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a po entity.
     *
     * @Route("/{id}", name="po_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        //$manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $po = $this->getDoctrine()->getRepository('AppBundle:Po')->find($id);
        $bcs = $this->getDoctrine()->getRepository('AppBundle:Bc')->findByPo($po);
        $deleteForm = $this->createDeleteForm($po,'po_index');

        return $this->render('@App/Po/show.html.twig', array(
            'po'            => $po,
            'bcs'           => $bcs,
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing po entity.
     *
     * @Route("/{id}/edit", name="po_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request,$id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $po = $this->getDoctrine()->getRepository('AppBundle:Po')->find($id);
        $deleteForm = $this->createDeleteForm($po,'po_index');
        $editForm = $this->createForm('AppBundle\Form\PoType', $po);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('po_index');
        }

        return $this->render('@App/Po/edit.html.twig', array(
            'po' => $po,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("Po/{id}/delete",name="po_delete",options = { "expose" = true })
     */
    public function deleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $po = $manager->getRepository('AppBundle:Po')->find($id);
        $manager->remove($po);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            /* $cryptage = $this->container->get('my.cryptage');
            $id = $po->getId();
            $id = $cryptage->my_encrypt($id); */
        }

        return $this->redirect($this->generateUrl('po_index'));
    }

    /**
     * Creates a form to delete a po entity.
     *
     * @param Po $po The po entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    
    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="site_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('site', $field, $type);
        return $this->redirect($this->generateUrl('site'));
    }
    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /** 
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $request->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }
    protected function saveFilter(FormInterface $form, $name, $route = null, array $params = null)
    {
        //$request = $this->getRequest();
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $url = $this->generateUrl($route ?: $name, is_null($params) ? array() : $params);
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.' . $name, $request->query->get($form->getName()));
            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);
            return $this->redirect($url);
        }
    }
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {   //dump($form);
        
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                //dump($form->get('num')->getData());
                //dump($form->get('active')->getData());
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }
        // possible sorting
        // nombre de ligne
        $session = $this->get('session');
        $nbr_pages = $session->get("nbr_pages");
        if ($nbr_pages == null){
            $nbr_pages = 20;
        };
        $this->addQueryBuilderSort($qb, $name);
        $request = $this->container->get('request_stack')->getCurrentRequest();
        return $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), $nbr_pages);
    }
    protected function getFilter($name)
    {   $request = $this->container->get('request_stack')->getCurrentRequest();
        return $request->getSession()->get('filter.' . $name);
    }
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }
    protected function getOrder($name)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }    
}
