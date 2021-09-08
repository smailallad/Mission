<?php

namespace AppBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\InterventionEntretienFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/intent")
 * @Security("has_role('ROLE_CHEF-PARK')")
 */
class InterventionEntretienController extends Controller
{
    /**
     *  * @Route("/index",name="interventionEntretien_index")
     */
    public function indexAction()
    {   $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(InterventionEntretienFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'interventionEntretien', 'interventionEntretien_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:InterventionEntretien')->getInterventions();
        $paginator = $this->filter($form, $qb, 'interventionEntretien');
        $forme=$form->createView();
        return $this->render('@App/InterventionEntretien/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));

    }
//*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="interventionEntretien_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('interventionEntretien', $field, $type);
        return $this->redirect($this->generateUrl('interventionEntretien_index'));
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
    {   //dump($qb->getDQL());
        $manager = $this->getDoctrine()->getManager();
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                //$this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
                $qb = $manager->getRepository('AppBundle:InterventionEntretien')->addFilter($qb,$form);
            }
        }
        //dump($qb->getDQL());
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
    /*
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }*/
    protected function getOrder($name) 
    {   
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return  $session->has('sort.' . $name) ? $session->get('sort.' . $name) : array("field" => "entretienVehicule.id","type" => "DESC");
        
    }

}
