<?php
namespace AppBundle\Controller;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\InterventionVehicule;
use AppBundle\Form\InterventionVehiculeType;
use AppBundle\Entity\KmsInterventionVehicule;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\KmsInterventionVehiculeType;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\InterventionVehiculeFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/interventionVehicule")
 * @Security("has_role('ROLE_ROLLOUT')")
 */
class InterventionVehiculeController extends Controller
{
    /**
     * @Route("/interventionVehicule",name="interventionVehicule_index")
     */
    public function indexAction()
    {   
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(InterventionVehiculeFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'interventionVehicule', 'interventionVehicule_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:InterventionVehicule')->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'interventionVehicule');
        $forme=$form->createView();
        return $this->render('@App/InterventionVehicule/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/new",name="interventionVehicule_new")
     */
    public function newAction(Request $request)
    {
        $interventionVehicule = new InterventionVehicule();
        $form = $this->createForm(InterventionVehiculeType::class, $interventionVehicule);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($interventionVehicule);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('interventionVehicule_index', array('id' => $cryptage->my_encrypt($interventionVehicule->getId()))));
        }
        return $this->render('@App/InterventionVehicule/new.html.twig', array(
            'interventionVehicule' => $interventionVehicule,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="interventionVehicule_edit")
     * id : interventionVehicule
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $interventionVehicule = $this->getDoctrine()->getRepository('AppBundle:InterventionVehicule')->find($id);
        $editForm = $this->createForm(InterventionVehiculeType::class, $interventionVehicule, array(
            'action' => $this->generateUrl('interventionVehicule_edit', array('id' => $cryptage->my_encrypt($interventionVehicule->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('interventionVehicule_index', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/InterventionVehicule/edit.html.twig', array(
            'interventionVehicule'       => $interventionVehicule,
            'edit_form'     => $editForm->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/show/{kmsInterventionId}",name="interventionVehicule_show")
     * id : interventionVehicule
     */
    public function showAction($id,$kmsInterventionId = null , Request $request)
    {   
        $kmsInterventionParam = $kmsInterventionId;
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $interventionVehicule = $this->getDoctrine()->getRepository('AppBundle:InterventionVehicule')->find($id);
        $kmsIntervention = null;
        $kmsInterventions = $this->getDoctrine()->getRepository('AppBundle:KmsInterventionVehicule')->findByInterventionVehicule($interventionVehicule);
        if ($kmsInterventionId <> null) {
            $kmsInterventionId = $cryptage->my_decrypt($kmsInterventionId);
            dump($kmsInterventionId);
            $kmsIntervention = $this->getDoctrine()->getRepository('AppBundle:KmsInterventionVehicule')->find($kmsInterventionId);
            //$kmsIntervention = $kmsIntervention->getInterventionVehicule();
            $methode = 'Edit';
        }else{
            $kmsIntervention = new KmsInterventionVehicule();
            $kmsIntervention->setInterventionVehicule($interventionVehicule);
            $methode = 'Post';
        }
        
        $deleteForm = $this->createDeleteForm($id, 'interventionVehicule_delete');
        //$deleteKmsForm = $this->createDeleteForm($id, 'kms_intervention_delete');
        $form = $this->createForm(KmsInterventionVehiculeType::class, $kmsIntervention, array(
            'action'        => $this->generateUrl('interventionVehicule_show', array(
                                                                            'id' => $cryptage->my_encrypt($id),
                                                                            'kmsInterventionId' => $kmsInterventionParam,
                                                                        )),

            'method'        => 'PUT',

        ));

        if ($form->handleRequest($request)->isValid()) {
                $manager->persist($kmsIntervention);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirect($this->generateUrl('interventionVehicule_show', array('id' => $cryptage->my_encrypt($id))));
        }
      
        return $this->render('@App/InterventionVehicule/show.html.twig', array(
            'interventionVehicule'      => $interventionVehicule,
            'kmsInterventions'          => $kmsInterventions,
            'delete_form'               => $deleteForm->createView(),
            //'delete_kms_form'           => $deleteKmsForm->createView(),
            'form'                      => $form->createView(),
            'methode'                   => $methode,
        ));
    }
    /**
     * @Route("/{id}/delete",name="interventionVehicule_delete",options = { "expose" = true })
     *
     */
    public function deleteAction(InterventionVehicule $interventionVehicule, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($interventionVehicule);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $interventionVehicule->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('interventionVehicule_index', array('id' => $id)));
        }
        return $this->redirect($this->generateUrl('interventionVehicule'));
    }

    /**
     * @Route("/{id}/KmsDelete/{intervention}",name="kms_intervention_delete",options = { "expose" = true })
     */
    public function kmsInterventiondeleteAction($id,$intervention)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $kms = $manager->getRepository('AppBundle:KmsInterventionVehicule')->find($id);
        $manager->remove($kms);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
        }

        return $this->redirect($this->generateUrl('interventionVehicule_show', array('id' => $cryptage->my_encrypt($intervention))));
    }
    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="interventionVehicule_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('interventionVehicule', $field, $type);
        return $this->redirect($this->generateUrl('interventionVehicule_index'));
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
        //dump($nbr_pages);
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
