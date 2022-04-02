<?php
namespace AppBundle\Controller;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\EntretienVehicule;
use AppBundle\Form\EntretienFilterType;
use AppBundle\Form\EntretienVehiculeType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\InterventionEntretien;
use AppBundle\Form\InterventionEntretienType;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
    {
        $cryptage = $this->container->get('my.cryptage');
        $entretien = new EntretienVehicule();
        $form = $this->createForm(EntretienVehiculeType::class, $entretien);
        if ($form->handleRequest($request)->isValid()) {
            $lastEntretien = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getLastEntretien($entretien->getVehicule());
            //$entretien2 = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getApres($entretien->getDate());
            if ($entretien->getDate()<=$lastEntretien->getDate()){
                $this->get('session')->getFlashBag()->add('danger', 'Date doit ete superieur à : ' . date_format($lastEntretien->getDate(),'d/m/Y') );
            }elseif ($entretien->getKms() <= $lastEntretien->getKms()){
                $this->get('session')->getFlashBag()->add('danger', 'Kms doit ete superieur à : ' . $lastEntretien->getKms() );
            }else{
                $this->getDoctrine()->getManager()->persist($entretien);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                return $this->redirect($this->generateUrl('entretien_show', array('id' => $cryptage->my_encrypt($entretien->getId()))));
            }
        }
        return $this->render('@App/Entretien/new.html.twig', array(
            'entretien'     => $entretien,
            'form'          => $form->createView(),
        ));        
    }
    /**
     * @Route("/{id}/edit",name="entretien_edit")
     */
    public function editAction(Request $request,$id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $entretien = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->find($id);
        $entretienActuel = clone $entretien;
        $editForm = $this->createForm(EntretienVehiculeType::class, $entretien, array(
            'action' => $this->generateUrl('entretien_edit', array('id' => $cryptage->my_encrypt($entretien->getId()))),
            'method' => 'PUT',
        ));
        $dateActuel = $entretien->getDate();
        if ($editForm->handleRequest($request)->isValid()) {
            $entretienPreview = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getPreview($entretienActuel);
            $entretienNext = $this->getDoctrine()->getRepository('AppBundle:EntretienVehicule')->getNext($entretienActuel);
            if ($entretienNext != null){
                if ($entretienPreview != null){
                    if (($entretien->getDate() >= $entretienPreview->getDate()) && ($entretien->getDate() <= $entretienNext->getDate())){
                        if ($entretien->getDate() > new DateTime(date('Y-m-d')) ){
                            $this->get('session')->getFlashBag()->add('danger', 'Date doit etre inférieur ou égale à : ' . date('d/m/Y'));
                        }else{
                            if ($entretien->getKms()>=$entretienPreview->getKms() && $entretien->getKms()<=$entretienNext-getKms()){
                                $this->getDoctrine()->getManager()->flush();
                                $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                            }else{
                                $this->get('session')->getFlashBag()->add('danger', 'Kms doit etre entre : ' .$entretienPreview->getKms().' et : '. $entretienNext->getKms());        
                            }
                        }
                    }else{
                        $this->get('session')->getFlashBag()->add('danger', 'Date doit etre entre : ' .date_format($entretienPreview->getDate(),"d/m/Y") .' et : '. date_format($entretienNext->getDate(),"d/m/Y"));
                    }
                }elseif ($entretien->getDate() <= $entretienNext->getDate()){
                    if ($entretien->getKms()<=$entretienNext-getKms()){
                        $this->getDoctrine()->getManager()->flush();
                        $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    }else{
                        $this->get('session')->getFlashBag()->add('danger', 'Kms doit etre supérieur à : ' . $entretienNext->getKms());
                    }
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date doit etre supérieur ou égale à : ' . date_format($entretienNext->getDate(),"d/m/Y"));
                }
            }elseif ($entretienPreview != null){
                if ($entretien->getDate() >= $entretienPreview->getDate()){
                    if ($entretien->getDate() > new DateTime(date('Y-m-d')) ){
                        $this->get('session')->getFlashBag()->add('danger', 'Date doit etre inférieur ou égale à : ' . date('d/m/Y'));
                    }else{
                        if ($entretien->getKms()>=$entretienPreview->getKms()){
                            $this->getDoctrine()->getManager()->flush();
                            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                        }else{
                            $this->get('session')->getFlashBag()->add('danger', 'Kms doit etre inférieur à : ' . $entretienPreview->getKms());
                        }
                    }
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date doit etre superieur ou égale à : ' .date_format($entretienPreview->getDate(),"d/m/Y" ));
                }
            }else{
                dump('ok4');
            }
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


    //*********************************************************************************
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
    
    protected function getOrder($name)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }
}
