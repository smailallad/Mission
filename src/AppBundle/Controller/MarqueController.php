<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Marque;
use AppBundle\Form\MarqueType;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\KmsMarqueType;
use AppBundle\Form\MarqueFilterType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\KmsInterventionVehicule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/marque")
 * @Security("has_role('ROLE_CHEF_PARK')")
 */
class MarqueController extends Controller
{
    /**
     * @Route("/new",name="marque_new")
     */
    public function newAction(Request $request)
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($marque);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('marque_index'));
        }
        return $this->render('@App/Marque/new.html.twig', array(
            'marque' => $marque,
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/{id}/edit",name="marque_edit")
     */
    public function editAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $marque = $this->getDoctrine()->getRepository('AppBundle:Marque')->find($id);
        $editForm = $this->createForm(MarqueType::class, $marque, array(
            'action' => $this->generateUrl('marque_edit', array('id' => $cryptage->my_encrypt($marque->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('marque_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Marque/edit.html.twig', array(
            'marque'      => $marque,
            'edit_form'     => $editForm->createView(),
        ));
        
    }
    

    /**
     * @Route("/index",name="marque_index")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(MarqueFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'marque', 'marque_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Marque')->createQueryBuilder('v');
        $paginator = $this->filter($form, $qb, 'marque');
        $forme=$form->createView();
        return $this->render('@App/Marque/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
        
    }

    
    /**
     * @Route("/{id}/show/{kmsInterventionId}",name="marque_show")
     * id : marque
     */
    public function showAction($id,$kmsInterventionId = null , Request $request)
    {   
        $kmsInterventionParam = $kmsInterventionId;
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $marque = $this->getDoctrine()->getRepository('AppBundle:Marque')->find($id);
        $kmsIntervention = null;
        $kmsInterventions = $this->getDoctrine()->getRepository('AppBundle:KmsInterventionVehicule')->findByMarque($marque);
        if ($kmsInterventionId <> null) {
            $kmsInterventionId = $cryptage->my_decrypt($kmsInterventionId);
           
            $kmsIntervention = $this->getDoctrine()->getRepository('AppBundle:KmsInterventionVehicule')->find($kmsInterventionId);
            //$kmsIntervention = $kmsIntervention->getInterventionVehicule();
            $methode = 'Edit';
        }else{
            $kmsIntervention = new KmsInterventionVehicule();
            $kmsIntervention->setMarque($marque);
            $methode = 'Post';
        }
       
        $deleteForm = $this->createDeleteForm($id, 'marque_delete');
        //$deleteKmsForm = $this->createDeleteForm($id, 'kms_intervention_delete');
        $form = $this->createForm(KmsMarqueType::class, $kmsIntervention, array(
            'action'        => $this->generateUrl('marque_show', array(
                                                                            'id' => $cryptage->my_encrypt($id),
                                                                            'kmsInterventionId' => $kmsInterventionParam,
                                                                        )),

            'method'        => 'PUT',

        ));

        if ($form->handleRequest($request)->isValid()) {
                $manager->persist($kmsIntervention);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirect($this->generateUrl('marque_show', array('id' => $cryptage->my_encrypt($id))));
        }
        
        return $this->render('@App/Marque/show.html.twig', array(
            'marque'                    => $marque,
            'kmsInterventions'          => $kmsInterventions,
            'delete_form'               => $deleteForm->createView(),
            //'delete_kms_form'           => $deleteKmsForm->createView(),
            'form'                      => $form->createView(),
            'methode'                   => $methode,
        ));
    }


    /**
     * @Route("/{id}/delete",name="marque_delete",options = { "expose" = true })
     */
    public function deleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $marque = $manager->getRepository('AppBundle:Marque')->find($id);
        $manager->remove($marque);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $marque->getId();
            $id = $cryptage->my_encrypt($id);
        }

        return $this->redirect($this->generateUrl('marque_index'));
    }

    /**
     * @Route("/{id}/KmsDelete/{marque}",name="kmsMarque_delete",options = { "expose" = true })
     */
    public function kmsMarqueDeleteAction($id,$marque)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $kms = $manager->getRepository('AppBundle:KmsInterventionVehicule')->find($id);
        $manager->remove($kms);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            
            //$id = $marque->getId();
            //$id = $cryptage->my_encrypt($id);
        }

        return $this->redirect($this->generateUrl('marque_show', array('id' => $cryptage->my_encrypt($marque))));
    }




    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="marque_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('marque', $field, $type);
        return $this->redirect($this->generateUrl('marque_index'));
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
