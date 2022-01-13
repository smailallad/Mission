<?php
namespace AppBundle\Controller;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\Prestation;
use AppBundle\Form\PrestationType;
use AppBundle\Entity\BcPrestation;
use AppBundle\Form\BcPrestationType;
use AppBundle\Form\PrestationFilterType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Form\BcPrestationNewType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/prestation")
 * @Security("has_role('ROLE_GERANT')")
 */
class PrestationController extends Controller
{
    /**
     * @Route("/prestation",name="prestation")
     */
    public function indexAction()
    {   
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(PrestationFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'prestation', 'prestation'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Prestation')->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'prestation');
        $forme=$form->createView();
        return $this->render('@App/Prestation/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/new",name="prestation_new")
     */
    public function newAction(Request $request)
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($prestation);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('prestation_show', array('id' => $cryptage->my_encrypt($prestation->getId()))));
        }
        return $this->render('@App/Prestation/new.html.twig', array(
            'prestation' => $prestation,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="prestation_edit")
     * id : prestation
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $prestation = $this->getDoctrine()->getRepository('AppBundle:Prestation')->find($id);
        $editForm = $this->createForm(PrestationType::class, $prestation, array(
            'action' => $this->generateUrl('prestation_edit', array('id' => $cryptage->my_encrypt($prestation->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('prestation_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Prestation/edit.html.twig', array(
            'prestation'    => $prestation,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/{id}/show",name="prestation_show")
     * id : prestation
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $prestation = $this->getDoctrine()->getRepository('AppBundle:Prestation')->find($id);
        $bcPrestations = $this->getDoctrine()->getRepository('AppBundle:BcPrestation')->findByPrestation($id);
        $editMontantForm = $this->createForm(BcPrestationType::class, New BcPrestation, array(
            'action' =>  $this->generateUrl('prestation'),
            'method' => 'POST',
        ));
        $newMontantForm = $this->createForm(BcPrestationNewType::class, New BcPrestation, array(
            'prestation'   => $prestation,
            'action' => $this->generateUrl('prestation'),
            'method' => 'POST',
            
        ));
        //$form_realisateur = $this->createForm(InterventionUserType::class,$interventionUser,array('id'=>$intervention));
        $deleteForm = $this->createDeleteForm($id, 'prestation_delete');
        return $this->render('@App/Prestation/show.html.twig', array(
            'bcPrestations'                 => $bcPrestations,
            'prestation'                    => $prestation,
            'delete_form'                   => $deleteForm->createView(),
            'edit_montant_form'             => $editMontantForm->createView(),
            'new_montant_form'              => $newMontantForm->createView(),
            )
        );
    }
    /**
     * @Route("/{id}/delete",name="prestation_delete")
     *
     */
    public function deleteAction(Prestation $prestation, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($prestation);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $prestation->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('prestation_show', array('id' => $id)));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('prestation'));
    }

    /**
     * @Route("/tarif/prestation/zone/{id}/delete",name="bcPrestation_delete",options = { "expose" = true })
     *
     */
    public function bcPrestationZoneDeleteAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        
        $manager = $this->getDoctrine()->getManager();
        $bcPrestation = $this->getDoctrine()->getRepository('AppBundle:BcPrestation')->find($id);
        $prestation = $bcPrestation->getPrestation();
        $id = $prestation->getId();
        $id = $cryptage->my_encrypt($id);

        $manager->remove($bcPrestation);
        try {
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
        }
        return $this->redirect($this->generateUrl('prestation_show', array('id' => $id)));
    }

    /**
     * @Route("/tarif/prestation/zone/{id}/edit",name="bcPrestation_edit",options = { "expose" = true })
     *
     */
    public function bcPrestationZoneEditAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        //$id = $cryptage->my_decrypt($id);
        $bcPrestation = $this->getDoctrine()->getRepository('AppBundle:BcPrestation')->find($id);
        $id = $bcPrestation->getPrestation()->getId();
        $editMontantForm = $this->createForm(BcPrestationType::class, $bcPrestation);
        
        if ($editMontantForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
        }else{
            $this->get('session')->getFlashBag()->add('danger', 'Données non validée.');            
        }
        return $this->redirect($this->generateUrl('prestation_show', array('id' => $cryptage->my_encrypt($id))));
    }

     /**
     * @Route("/tarif/prestation/zone/{id}/new",name="bcPrestation_new",options = { "expose" = true })
     *
     */
    public function bcPrestationZoneNewAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        //$id = $cryptage->my_decrypt($id);
        $prestation = $this->getDoctrine()->getRepository('AppBundle:Prestation')->find($id);
        $bcPrestation = new BcPrestation;
        $bcPrestation->setPrestation($prestation);
        $newMontantForm = $this->createForm(BcPrestationNewType::class, $bcPrestation);
        if ($newMontantForm->handleRequest($request)->isValid()) {
            $manager->persist($bcPrestation);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
        }else{
            $this->get('session')->getFlashBag()->add('danger', 'Données non validée.');            
        }
        return $this->redirect($this->generateUrl('prestation_show', array('id' => $cryptage->my_encrypt($id))));
    }

    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="prestation_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('prestation', $field, $type);
        return $this->redirect($this->generateUrl('prestation'));
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
