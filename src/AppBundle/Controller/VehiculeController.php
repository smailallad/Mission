<?php

namespace AppBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\VehiculeType;
use AppBundle\Form\VehiculeFilterType;
use AppBundle\Form\VehiculeReleverType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/vehicule")
 * @Security("has_role('ROLE_CHEF_PARK')")
 */
class VehiculeController extends Controller
{
    /**
     * @Route("/new",name="vehicule_new")
     */
    public function newAction()
    {
        return $this->render('@App/Vehicule/new.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/{id}/edit",name="vehicule_edit")
     */
    public function editAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $vehicule = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->find($id);
        $editForm = $this->createForm(VehiculeType::class, $vehicule, array(
            'action' => $this->generateUrl('vehicule_edit', array('id' => $cryptage->my_encrypt($vehicule->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('vehicule_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Vehicule/edit.html.twig', array(
            'vehicule'      => $vehicule,
            'edit_form'     => $editForm->createView(),
        ));
        
    }
    /**
     * @Route("/{id}/edit/relever",name="vehicule_edit_relever")
     */
    public function editReleverAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $vehicule = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->find($id);
        $lastEntretien = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getLastEntretien($id);
        $editForm = $this->createForm(VehiculeReleverType::class, $vehicule, array(
            'action' => $this->generateUrl('vehicule_edit_relever', array('id' => $cryptage->my_encrypt($vehicule->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            
            dump($vehicule);
            dump($lastEntretien);
            //throw new \Exception('Message');
            if ($vehicule->getDateRelever() < $lastEntretien->getDate()){
                $this->get('session')->getFlashBag()->add('danger', 'Date incorrecte, elle doit etre superieur à : ' . date_format($lastEntretien->getDate(),"d/m/Y"));
                return $this->redirect($this->generateUrl('vehicule_edit_relever', array('id' => $cryptage->my_encrypt($id))));
            }
            if ($vehicule->getKmsRelever() < $lastEntretien->getKms()){
                $this->get('session')->getFlashBag()->add('danger', 'Kms incorrecte, il doit etre superieur à : ' . $lastEntretien->getKms());
                return $this->redirect($this->generateUrl('vehicule_edit_relever', array('id' => $cryptage->my_encrypt($id))));
            }

            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('vehicule_index'));
        }
        return $this->render('@App/Vehicule/edit_relever.html.twig', array(
            'vehicule'      => $vehicule,
            'edit_form'     => $editForm->createView(),
        ));
        
    }

    /**
     * @Route("/index",name="vehicule_index")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(VehiculeFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'vehicule', 'vehicule_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Vehicule')->createQueryBuilder('v');
        $paginator = $this->filter($form, $qb, 'vehicule');
        $forme=$form->createView();
        return $this->render('@App/Vehicule/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
        
    }

    /**
     * @Route("/show",name="vehicule_show")
     */
    public function showAction()
    {
        return $this->render('@App/Vehicule/show.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/delete",name="vehicule_delete")
     */
    public function deleteAction()
    {
        return $this->render('@App/Vehicule/delete.html.twig', array(
            // ...
        ));
    }



    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="vehicule_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('vehicule', $field, $type);
        return $this->redirect($this->generateUrl('vehicule_index'));
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
    {   
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }
        // possible sorting
        // nombre de ligne
        $session = $this->get('session');
        $nbr_pages = $session->get("nbr_pages");
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
