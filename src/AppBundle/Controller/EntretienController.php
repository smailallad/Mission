<?php

namespace AppBundle\Controller;
use AppBundle\Entity\EntretienVehicule;
use AppBundle\Form\EntretienFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/entretion")
 * @Security("has_role('ROLE_CHEF-PARK')")
 */
class EntretienController extends Controller
{
    /**
     * @Route("/index",name="entretien_index")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EntretienFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'entretien', 'entretien_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:EntretienVehicule')->getEntretiens();
        $paginator = $this->filter($form, $qb, 'entretien');
        $forme=$form->createView();
        return $this->render('@App/Entretien/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
        
    }
    /**
     * @Route("/{id}/show",name="entretien_show")
     * id : entretien
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $entretien = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->find($id);
        $interventions = $this->getDoctrine()->getRepository('AppBundle:InterventionEntretien')->findByEntretienVehicule($entretien);
        dump($interventions);
        $deleteForm = $this->createDeleteForm($id, 'entretien_delete');
        return $this->render('@App/Entretien/show.html.twig', array(
            'entretien'         => $entretien,
            'interventions'     => $interventions,
            'delete_form'       => $deleteForm->createView()));
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

    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="entretien_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('entretien', $field, $type);
        return $this->redirect($this->generateUrl('entretien_index'));
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
        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }


}
