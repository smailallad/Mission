<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Facture;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\FactureType;
use AppBundle\Form\FactureFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
     * @Route("/facture")
     * @Security("has_role('ROLE_FACTURATION')")
     */

class FactureController extends Controller
{
    /**
     * @Route("/index",name="facture_index")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(FactureFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'facture', 'facture_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Facture')->listeFactures();
        $paginator = $this->filter($form, $qb, 'facture');
        $forme=$form->createView();
        return $this->render('@App/Facture/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
        
    }
    /**
     * @Route("/{id}/edit",name="facture_edit")
     */
    public function editAction($id,Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($id);
        $bc = $facture->getBc();
        
        $editForm = $this->createForm(FactureType::class, $facture, [
            'action'    => $this->generateUrl('facture_edit', [
                'id'    => $cryptage->my_encrypt($facture->getId()),
            ]),
            'method'    => 'PUT',
            'vbc'       => $bc,
        ]);
        if ($editForm->handleRequest($request)->isValid()) 
        {           
            $date = $facture->getDate();
            $interventions = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getInterventionsFactureApresDate($facture,$date);
            $nbr = count($interventions);
            
            if ($nbr > 0)
            { 
                $this->get('session')->getFlashBag()->add('danger','Vous avez : ' . $nbr . ' intervention(s) supperieur à la date de la facture, modification de la date impossible. ');
            }else
            {
                if ($facture->getBc() != null )
                { 
                    if ($date < $facture->getBc()->getdate())
                    {
                        $this->get('session')->getFlashBag()->add('danger','La date doit etre supperieur ou égale à : ' . date_format($facture->getBc()->getdate(),'d/m/Y') . '.');
                    }else
                    {
                        $this->getDoctrine()->getManager()->flush();
                        return $this->redirect($this->generateUrl('facture_show', ['id' => $cryptage->my_encrypt($facture->getId())]));
                    }
                }else
                {
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirect($this->generateUrl('facture_show', ['id' => $cryptage->my_encrypt($facture->getId())]));
                }
            }
            
        }
        return $this->render('@App/Facture/edit.html.twig', [
            'facture' => $facture,
            'edit_form' => $editForm->createView(),
        ]);
    }
    /**
     * @Route("/{id}/show",name="facture_show")
     */
    public function showAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($id);
        $interventions = $this->getDoctrine()->getRepository('AppBundle:Intervention')->findByFacture($facture);
        $somme = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getSommeFacture($id);
        $deleteForm = $this->createDeleteForm($id, 'facture_delete');
        return $this->render('@App/Facture/show.html.twig', [
            'facture'       => $facture,
            'somme'         => $somme,
            'interventions' => $interventions,
            'delete_form'   => $deleteForm->createView(),
        ]);
        
    }

    /**
     * @Route("/new",name="facture_new")
     */
    public function newAction(Request $request)
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($facture);
            $manager->flush();
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('facture_show', ['id' => $cryptage->my_encrypt($facture->getId())]));
        }
        return $this->render('@App/Facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete",name="facture_delete")
     *
     */
    public function deleteAction(Facture $facture)
    {   
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        //$id = $cryptage->my_decrypt($id);
        //$facture = $manager->getRepository('AppBundle:Facture')->fid($id);
        $manager->remove($facture);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $id = $facture->getId();
            $id = $cryptage->my_encrypt($id);
            //return $this->redirect($this->generateUrl('facture_show', array('id' => $id)));
            return $this->redirect($this->generateUrl('facture_show', ['id' => $cryptage->my_encrypt($facture->getId())]));
        }

        return $this->redirect($this->generateUrl('facture_index'));

    }

    /**
     * @Route("/{id}/facture/prestation/delete",name="prestation_facture_delete",options ={"expose" = true})
     */
    public function facturePrestationDeleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $intervention = $manager->getRepository('AppBundle:Intervention')->find($id);
        $facture = $intervention->getFacture();
        $intervention->setFacture(null);
        $intervention->setPrestationBc(null);
        $manager->flush();
        //dump($intervention);
        return $this->redirect($this->generateUrl('facture_show',array('id' => $cryptage->my_encrypt($facture->getId()))));
    }

    /**
     * @Route("/{facture}/prestations/non/facturer",name="interventions_non_facturer")
     */
    
    public function prestationsNonFacturerAction($facture)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($facture);
        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($id);
        $projet  = $facture->getBc()->getProjet();
        $prestations = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getInterventionsNonFacturer($facture->getDate(),$projet);
        return $this->render('@App/Facture/interventionsNonFacturer.html.twig', [
            'prestations'   => $prestations,
            'facture'       => $facture,
        ]);
    }

    /**
     * @Route("/{facture}/facture/add/prestations", name="facture_add_prestations")
     */
    public function factureAddPrestationsAction($facture,Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($facture);
        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($id);
        $prestations = $request->request->all();
        foreach ($prestations as $prestation) {
            
            $intervention = $manager->getRepository('AppBundle:Intervention')->find($prestation);
            $intervention->setFacture($facture);
            $manager->flush();        
        }
        return $this->redirect($this->generateUrl('facture_show',array('id' => $cryptage->my_encrypt($facture->getId()))));
        //throw new \Exception('Arret Smail');
    }

    /**
     * @Route("/prestationBc_non_associer/{intervention}", name="prestationBc_non_associer")
     */
    public function prestationBcNonAssocierAction($intervention)
    {
        $cryptage       = $this->container->get('my.cryptage'); 
        $intervention   = $cryptage->my_decrypt($intervention);
        $intervention = $this->getDoctrine()->getRepository('AppBundle:Intervention')->find($intervention);

        $facture        = $intervention->getFacture();
        $bc             = $facture->getBc();
        $prestation     = $intervention->getPrestation();
        $zone           = $intervention->getSite()->getWilaya()->getZone();
        
        $sites       = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->getSitesBc($bc);
        $avecSite = count($sites) > 0 ? true : false ;
        $site = $avecSite ? $intervention->getSite() : null ;
        $prestationBcs = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->getPrestationIntervention($bc,$prestation,$zone,$site);
        $prestationBc=[];
        $prestationBc2=[];
        foreach ($prestationBcs as $prestationBc1) {
            
            $quantite = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getSommeQuantitePrestationBc($prestationBc1);
            if ($quantite < $prestationBc1->getQuantite() )
            {
                $prestationBc[] =   $prestationBc1;
                $prestationBc2[]=   ["prestation"           => $prestationBc1,
                                    "quantiteRester"       => $prestationBc1->getQuantite() - $quantite,
                                    "quantiteIntervention"  => $intervention->getQuantite(),
                                    ];
            }
        }
        return $this->render('@App/Facture/prestationBcsNonAssocier.html.twig', [
            'prestationBcs'     => $prestationBc,
            'facture'           => $facture->getId(),
            'intervention'      => $intervention->getId(),
            'prestationBc2'     => $prestationBc2,
            'avecSite'          => $avecSite,
        ]);
    }
    /**
     * @Route("/interventionBcDessocier/{intervention}", name="prestationBc_dessocier")
     */
    public function prestationBcDessocierAction($intervention)
    {
        $cryptage = $this->container->get('my.cryptage');
        $intervention = $cryptage->my_decrypt($intervention);
        $intervention = $this->getDoctrine()->getRepository('AppBundle:Intervention')->find($intervention);  
        $facture = $intervention->getFacture();
        $intervention->setPrestationBc(null);
        $this->getDoctrine()->getManager()->flush();     
        
        //return $this->redirect($this->generateUrl('facture_show',array('id' => $facture->getId())));
        return $this->redirect($this->generateUrl('facture_show', ['id' => $cryptage->my_encrypt($facture->getId())]));
    }

    /**
     * @Route("/associerPrestation/{prestationBc}/intervention/{intervention}", name="associer_prestation")
     */
    public function associerPrestationAction($prestationBc,$intervention)
    {
        $cryptage = $this->container->get('my.cryptage');
        
        $prestationBc = $cryptage->my_decrypt($prestationBc);
        $intervention = $cryptage->my_decrypt($intervention);
        $facture = $intervention->getFacture();
        $prestationBc = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->find($prestationBc);
        $intervention = $this->getDoctrine()->getRepository('AppBundle:Intervention')->find($intervention);
        $quantite = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getSommeQuantitePrestationBc($prestationBc);
        
        if ($intervention->getQuantite() > ($prestationBc->getQuantite() - $quantite))
        {
            $msg = "Facturation impossible : Quatité à facturer : " . $intervention->getQuantite() . ", Quantité BC pour cette prestation est : " . $prestationBc->getQuantite() . ", Quantié facturer déja avec ce BC pour cette prestation est : " . $quantite . ", il vous reste que : " . ($prestationBc->getQuantite() - $quantite) . " au lieu de : " . $intervention->getQuantite() . ".";
            $this->get('session')->getFlashBag()->add('danger', $msg);
        }else
        {
            $intervention->setPrestationBc($prestationBc);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirect($this->generateUrl('facture_show',array('id' => $cryptage->my_encrypt($facture->getId()))));
        //return $this->redirect($this->generateUrl('facture_show', ['id' => $cryptage->my_encrypt($facture)]));
    }

    /**
     * @Route("/associerPrestationAuto/facture/{facture}", name="associer_prestation_Auto")
     */
    public function associerPrestationAutoAction($facture)
    {
        $cryptage = $this->container->get('my.cryptage');
        
        $facture = $cryptage->my_decrypt($facture);
        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($facture);
        $bc = $facture->getBc();
        $interventions = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getInterventionsNonAssocier($facture);
        foreach ($interventions as $intervention) 
        {
            $prestation     = $intervention->getPrestation();
            $zone           = $intervention->getSite()->getWilaya()->getZone();
            
            $sites       = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->getSitesBc($bc);
            $avecSite = count($sites) > 0 ? true : false ;
            $site = $avecSite ? $intervention->getSite() : null ;
            $prestationBcs = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->getPrestationIntervention($bc,$prestation,$zone,$site);
            foreach ($prestationBcs as $prestationBc) {
                $quantite = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getSommeQuantitePrestationBc($prestationBc);
                if ($quantite < $prestationBc->getQuantite() )
                {
                    if ($intervention->getQuantite() <= $prestationBc->getQuantite())
                    {
                        $intervention->setPrestationBc($prestationBc);
                        $this->getDoctrine()->getManager()->persist($intervention);
                        $this->getDoctrine()->getManager()->flush();
                    }
                }
            }

            
        }
        //dump($interventions);
        //throw new \Exception('Arret Smail');

        /*$intervention = $cryptage->my_decrypt($intervention);
        $facture = $intervention->getFacture();
        $prestationBc = $this->getDoctrine()->getRepository('AppBundle:PrestationBc')->find($prestationBc);
        $intervention = $this->getDoctrine()->getRepository('AppBundle:Intervention')->find($intervention);
        $quantite = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getSommeQuantitePrestationBc($prestationBc);
        
        if ($intervention->getQuantite() > ($prestationBc->getQuantite() - $quantite))
        {
            $msg = "Facturation impossible : Quatité à facturer : " . $intervention->getQuantite() . ", Quantité BC pour cette prestation est : " . $prestationBc->getQuantite() . ", Quantié facturer déja avec ce BC pour cette prestation est : " . $quantite . ", il vous reste que : " . ($prestationBc->getQuantite() - $quantite) . " au lieu de : " . $intervention->getQuantite() . ".";
            $this->get('session')->getFlashBag()->add('danger', $msg);
        }else
        {
            $intervention->setPrestationBc($prestationBc);
            $this->getDoctrine()->getManager()->flush();
        }*/

        return $this->redirect($this->generateUrl('facture_show',array('id' => $cryptage->my_encrypt($facture->getId()))));
    }


//*********************************************************************************
    /**
    * @route("/{field}/{type}/sort",name="facture_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {   $route_index ='facture_index';
        $nom_filtre = 'facture';
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
