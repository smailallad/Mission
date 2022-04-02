<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Bc;
use AppBundle\Entity\PrestationBc;
use AppBundle\Form\BcType;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\BcFilterType;
<<<<<<< HEAD
use AppBundle\Entity\PrestationBc;
use AppBundle\Form\PrestationBcType;
=======
use AppBundle\Form\PrestationBcType;
//use AppBundle\Entity\PrestationBc;
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
use Symfony\Component\Form\FormInterface;
use AppBundle\Form\SiteRechercheClientType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Route("/bc")
 * @Security("has_role('ROLE_FACTURATION')")
 */
class BcController extends Controller
{
    /**
     * @Route("/bs",name="bc_index")
     */
    public function indexAction()
    {   
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(BcFilterType::class);
        /*if (!is_null($response = $this->saveFilter($form, 'bc', 'bc_index'))) {
            return $response;
        }*/
      
        $qb = $manager->getRepository('AppBundle:Bc')->listeBcs();
        $paginator = $this->filter($form, $qb, 'bc');
        dump($paginator);
        $consomer = [];
        foreach ($paginator as $value) {
            $bcId = $value->getId();
            $consommer [strval($bcId)] = $manager->getRepository('AppBundle:Intervention')->getSommeBc($bcId);
        }
        //dump($consomer);


        return $this->render('@App/Bc/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
            'consommer'  => $consommer,
        ));
    }
    /**
     * @Route("/new",name="bc_new")
     */
    public function newAction(Request $request)
    {
        $bc = new Bc();
        $form = $this->createForm(BcType::class, $bc);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($bc);
            $manager->flush();
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('bc_index'));
        }
        return $this->render('@App/Bc/new.html.twig', array(
            'bc' => $bc,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="bc_edit")
     * id : bc
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $bc = $this->getDoctrine()->getRepository('AppBundle:Bc')->find($id);
        $editForm = $this->createForm(BcType::class, $bc, array(
            'action' => $this->generateUrl('bc_edit', array('id' => $cryptage->my_encrypt($bc->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('bc_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Bc/edit.html.twig', array(
            'bc'            => $bc,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/{id}/show",name="bc_show")
     * id : bc
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $bc             = $this->getDoctrine()->getRepository('AppBundle:Bc')->find($id);
        $prestationBcs  = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->findByBc($id);
        $somme          = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->getSomme($id);
        $deleteForm = $this->createDeleteForm($id, 'bc_delete');
        return $this->render('@App/Bc/show.html.twig', array(
            'bc'            => $bc,
            'somme'         => $somme,
            'prestationBcs' => $prestationBcs,
            'delete_form'   => $deleteForm->createView())
        );
    }

    /**
<<<<<<< HEAD
     * @Route("/bc/add/{id}/prestation",name="bc_add_prestation")
=======
     * @Route("/bd/add/{id}/prestation",name="bc_add_prestation")
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
     */
    public function bcAddPrestation($id,Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $prestationBc = new PrestationBc();
        $bc             = $this->getDoctrine()->getRepository('AppBundle:Bc')->find($id);
        $prestationBc->setBc($bc);
        $projet = $bc->getProjet();
        $client = $projet->getClient();
<<<<<<< HEAD
        $form_recherche_site = $this->createForm(SiteRechercheClientType::class);
        
        //$zone  = $this->getDoctrine()->getRepository('AppBundle:Zone')->findAll();
=======
        /* $form_realisateur = $this->createForm(InterventionUserType::class,$interventionUser,array('id'=>$intervention,'date'=>$intervention->getDateIntervention(),'mission'=>$mission)); */
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
        $form = $this->createForm(PrestationBcType::class,$prestationBc,['projet'=>$projet,"client"=>$client]);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
<<<<<<< HEAD
            $site = $request->request->get('prestation_bc')['siteId'];
            if ($site != null)
            {
                $site = $manager->getRepository('AppBundle:Site')->find($site);
                $prestationBc->setSite($site);
            }
=======
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
            $manager->persist($prestationBc);
            $manager->flush();
            return $this->redirect($this->generateUrl('bc_show', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Bc/add.prestation.html.twig', array(
<<<<<<< HEAD
            'bc'                    => $bc,
            'projet'                => $projet,
            'client'                => $client,
            'form'                  => $form->createView(),
            'form_recherche_site'   => $form_recherche_site->createView(),
            )
        );
    }

    /**
     * @Route("/bc/edit/{id}/prestation",name="bc_edit_prestation")
     */
    public function bcEditPrestation($id,Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $prestationBc = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->find($id);
        $bc = $prestationBc->getBc();
        $projet = $bc->getProjet();
        $client = $projet->getClient();
        $form_recherche_site = $this->createForm(SiteRechercheClientType::class);
        
        //$zone  = $this->getDoctrine()->getRepository('AppBundle:Zone')->findAll();
        $form = $this->createForm(PrestationBcType::class,$prestationBc,['projet'=>$projet,"client"=>$client]);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $site = $request->request->get('prestation_bc')['siteId'];
            if ($site != null)
            {
                $site = $manager->getRepository('AppBundle:Site')->find($site);
                $prestationBc->setSite($site);
            }
            //$manager->persist($prestationBc);
            $manager->flush();
            return $this->redirect($this->generateUrl('bc_show', array('id' => $cryptage->my_encrypt($bc->getId()))));
        }
        return $this->render('@App/Bc/edit.prestation.html.twig', array(
            'bc'                    => $bc,
            'prestationBc'          => $prestationBc,
            'projet'                => $projet,
            'client'                => $client,
            'form'                  => $form->createView(),
            'form_recherche_site'   => $form_recherche_site->createView(),
            )
=======
            'bc'            => $bc,
            'projet'        => $projet,
            'client'        => $client,
            'form'          => $form->createView())
>>>>>>> c4c86e1f6dacdb75bcb034443d12a868987ff8f1
        );
    }

    /**
     * @Route("/{id}/bc/delete/prestation",name="bc_delete_prestation",options ={"expose" = true})
     */
    public function bcDeletePrestationAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $prestationBc = $manager->getRepository('AppBundle:PrestationBc')->find($id);
        $bc = $prestationBc->getBc();
        $manager->remove($prestationBc);
        $manager->flush();
        return $this->redirect($this->generateUrl('bc_show',array('id' => $cryptage->my_encrypt($bc->getId()))));
    }
   
    /**
     * @Route("/{id}/delete",name="bc_delete")
     *
     */
    public function deleteAction(Bc $bc, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($bc);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $bc->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('bc_show', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('bc'));

    }
    
    //*********************************************************************************
    /**
     * Changer le nom de la route
     * 
    * @route("/{field}/{type}/sort",name="bc_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {   $route_index ='bc_index';
        $nom_filtre = 'bc';
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
