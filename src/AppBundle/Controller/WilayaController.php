<?php

namespace AppBundle\Controller;

use AppBundle\Form\WilayaType;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\WilayaFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/wilaya")
 * @Security("has_role('ROLE_SUPER_COMPTABLE')")
 */
class WilayaController extends Controller
{
    /**
     * @Route("/index",name="wilaya_index")
     */
    public function IndexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(WilayaFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'wialya', 'wilaya_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Wilaya')->getWilayas();
        $wilayas = $this->filter($form, $qb, 'wilaya');
        
        return $this->render('@App/Wilaya/index.html.twig', [
            'form' => $form->createView(),
            'wilayas' => $wilayas,
        ]);
    }

    /**
     * @Route("/new",name="wilaya_new")
     */
    public function NewAction()
    {
        return $this->render('@App/Wilaya/new.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/edit/{wilaya}",name="wilaya_edit")
     */
    public function editAction($wilaya, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $wilaya   = $cryptage->my_decrypt($wilaya);
        $wilaya   = $this->getDoctrine()->getRepository('AppBundle:Wilaya')->find($wilaya);
    
        $editForm  = $this->createForm(WilayaType::class, $wilaya, [
            'action'    => $this->generateUrl('wilaya_edit', [
            'wilaya'        => $cryptage->my_encrypt($wilaya->getId()),
            ]),
            'method' => 'PUT',
        ]);
 
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('wilaya_index'));
        }

        return $this->render('@App/Wilaya/edit.html.twig', [
            'wilaya'      => $wilaya,
            'edit_form' => $editForm->createView(),
            ]);

    }

    //*********************************************************************************
    /**
     * Changer le nom de la route
     * 
    * @route("/{field}/{type}/sort",name="wilaya_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {   $route_index ='wilaya_index';
        $nom_filtre = 'wilaya';
        $this->setOrder($nom_filtre, $field, $type);
        return $this->redirect($this->generateUrl($route_index));
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
        if ($nbr_pages == null){
            $nbr_pages = 20;
        };
        $nbr_pages = 1000;
        $this->addQueryBuilderSort($qb, $name);
        $request = $this->container->get('request_stack')->getCurrentRequest();
        return $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), $nbr_pages);
    }
    protected function getFilter($name)
    {   $request = $this->container->get('request_stack')->getCurrentRequest();
        return $request->getSession()->get('filter.' . $name);
    } 
    protected function fAlias(QueryBuilder $qb, $name){
        $joints = current($qb->getDQLPart('join'));
        if ($joints !== false) {
            foreach($joints as $joint){
                $valeur = explode(".",$joint->getJoin());
                if ($valeur[1] === $name){
                    return $joint->getAlias();
                }
            }
        }else{
            return false;
        }
    }
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {   
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $field= $order['field'];
            $field = explode(".",$field);
            if (count($field)>1){
                $qb->orderBy($this->fAlias($qb,$field[0]) . '.' . $field[1], $order['type']);
            }else{
                $qb->orderBy($alias . '.' . $order['field'], $order['type']);
            }
        }
    }
    
    protected function getOrder($name)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

}
