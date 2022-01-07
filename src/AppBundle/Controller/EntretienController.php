<?php

namespace AppBundle\Controller;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\EntretienVehicule;
use AppBundle\Form\EntretienFilterType;
use AppBundle\Form\EntretienVehiculeType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\InterventionEntretien;
use AppBundle\Form\InterventionEntretienType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

/**
 * @Route("/entretien")
 * @Security("has_role('ROLE_CHEF_PARK')")
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
     * @Route("/{id}/show/{interventionEntretien}",name="entretien_show")
     * id : entretien
     */
    public function showAction($id,$interventionEntretien = null , Request $request)
    {   
        $interventionEntretienParam = $interventionEntretien;
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $entretien = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->find($id);
        $intervention = null;
        $interventions = $this->getDoctrine()->getRepository('AppBundle:InterventionEntretien')->findByEntretienVehicule($entretien);
        if ($interventionEntretien <> null) {
            $interventionEntretien = $cryptage->my_decrypt($interventionEntretien);
            $interventionEntretien = $this->getDoctrine()->getRepository('AppBundle:InterventionEntretien')->find($interventionEntretien);
            $intervention = $interventionEntretien->getInterventionVehicule();
            $methode = 'Edit';
        }else{
            $interventionEntretien = new InterventionEntretien();
            $interventionEntretien->setEntretienVehicule($entretien);
            $methode = 'Post';
        }
        
        $deleteForm = $this->createDeleteForm($id, 'entretien_delete');
        $deleteInterventionForm = $this->createDeleteForm($id, 'intervention_entretien_delete');
        $form = $this->createForm(InterventionEntretienType::class, $interventionEntretien, array(
            'action'        => $this->generateUrl('entretien_show', array(
                                                                            'id' => $cryptage->my_encrypt($id),
                                                                            'interventionEntretien' => $interventionEntretienParam,
                                                                        )),

            'method'        => 'PUT',
            //'entretien'     => $entretien,
            //'intervention'  => $intervention,
        ));
        if ($form->handleRequest($request)->isValid()) {
                $manager->persist($interventionEntretien);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirect($this->generateUrl('entretien_show', array('id' => $cryptage->my_encrypt($id))));
        }
      
        return $this->render('@App/Entretien/show.html.twig', array(
            'entretien'                 => $entretien,
            'interventions'             => $interventions,
            'delete_form'               => $deleteForm->createView(),
            'delete_intervention_form'  => $deleteInterventionForm->createView(),
            'form'                      => $form->createView(),
            'methode'                   => $methode,
        ));
    }

    /**
     * @Route("/new",name="entretien_new")
     */
    public function newAction(Request $request)
    {/*
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($site);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('site_show', array('id' => $cryptage->my_encrypt($site->getId()))));
        }
        return $this->render('@App/Site/new.html.twig', array(
            'site' => $site,
            'form' => $form->createView(),
        ));


        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($id);
        $editForm = $this->createForm(SiteType::class, $site, array(
            'action' => $this->generateUrl('site_edit', array('id' => $cryptage->my_encrypt($site->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('site_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Site/edit.html.twig', array(
            'site'          => $site,
            'edit_form'     => $editForm->createView(),
        ));
        */
    }

    /**
     * @Route("/{id}/edit",name="entretien_edit")
     */
    public function editAction(Request $request,$id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $entretien = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->find($id);
        $editForm = $this->createForm(EntretienVehiculeType::class, $entretien, array(
            'action' => $this->generateUrl('entretien_edit', array('id' => $cryptage->my_encrypt($entretien->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $entretien1 = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getAvant($entretien->getDate());
            $entretien2 = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getApres($entretien->getDate());
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('entretien_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Entretien/edit.html.twig', array(
            'entretien'     => $entretien,
            'edit_form'     => $editForm->createView(),
        ));
    
       
    }

    /**
     * @Route("/{id}/intervention/entretien/delete",name="intervention_entretien_delete",options = { "expose" = true })
     * id 
     */ 
    public function InterventiontEntretienDeleteAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        
        //$id = $cryptage->my_decrypt($id);
       
        $intervention = $this->getDoctrine()->getRepository('AppBundle:InterventionEntretien')->find($id);
        $entretien = $intervention->getEntretienVehicule()->getId();
        $manager->remove($intervention);
        $manager->flush();
        return $this->redirect($this->generateUrl('entretien_show',array('id' => $cryptage->my_encrypt($entretien))));
    }

/**
     * @Route("/{id}/entretiendelete",name="entretien_delete",options = { "expose" = true })
     * id : entretien
     */
    public function EntretienVehiculeDeleAction($id,request $request){
        $manager = $this->getDoctrine()->getManager();
        $entretienVehicule = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->find($id);
        $manager->remove($entretienVehicule);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            
            //$this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $this->addFlash('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            //$id = $entretienVehicule->getId();
            $id = $cryptage->my_encrypt($id);
            //throw new \Exception('Message');
            return $this->redirect($this->generateUrl('entretien_show', array('id' => $id)));
        }
        $this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('entretien_index'));

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
