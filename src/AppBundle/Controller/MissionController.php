<?php
namespace AppBundle\Controller;
use AppBundle\Entity\User;
use AppBundle\Entity\Mission;
use AppBundle\Form\AvanceType;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\MissionType;
use AppBundle\Entity\Prestation;
use AppBundle\Form\NoteFraisType;
use AppBundle\Entity\FraisMission;
use AppBundle\Entity\Intervention;
use AppBundle\Entity\DepenseMission;
use AppBundle\Form\FraisMissionType;
use AppBundle\Form\InterventionType;
use AppBundle\Form\MissionFilterType;
use AppBundle\Form\SiteRechercheType;
use AppBundle\Entity\CarburantMission;
use AppBundle\Entity\InterventionUser;
use AppBundle\Form\DepenseMissionType;
use AppBundle\Form\NoteFraisFilterType;
use AppBundle\Form\CarburantMissionType;
use AppBundle\Form\InterventionUserType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Form\PrestationRechercheType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/mission")
 */
class MissionController extends Controller
{   
    
    /**
     * @Route("/index/mission",name="mission_index")
     */
    public function missionIndexAction()
    {   
        $session = $this->get("session");
        $session->set("appel_employe","non");
        $session->set("appel_journal","non");

        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(MissionFilterType::class);
        
        if (!is_null($response = $this->saveFilter($form, 'mission', 'mission_index'))) {
            return $response;
        }

        $qb             = $manager->getRepository('AppBundle:Mission')->getMissionAll('M');
        $qbAvance       = $manager->getRepository('AppBundle:Mission')->getAvanceMission('M');
        $qbDepense      = $manager->getRepository('AppBundle:DepenseMission')->getTotalDepenseMissions('M');
        $qbFM           = $manager->getRepository('AppBundle:FraisMission')->getTotalFM('M');
        $qbCarburant    = $manager->getRepository('AppBundle:CarburantMission')->getTotalCarburantMissions('M');

        $paginator = $this->filter($form,'mission', $qb,$qbAvance,$qbDepense,$qbFM,$qbCarburant);
        
        $SumAvance      = ($qbAvance->getQuery()->getResult()[0]['total']    === null) ? 0 : $qbAvance->getQuery()->getResult()[0]['total'] ;
        $SumDepense     = ($qbDepense->getQuery()->getResult()[0]['total']   === null) ? 0 : $qbDepense->getQuery()->getResult()[0]['total'];
        $SumFM          = ($qbFM->getQuery()->getResult()[0]['total']        === null) ? 0 : $qbFM->getQuery()->getResult()[0]['total'];
        $SumCarburant   = ($qbCarburant->getQuery()->getResult()[0]['total']        === null) ? 0 : $qbCarburant->getQuery()->getResult()[0]['total'];

        $SumDepense = $SumDepense + $SumFM + $SumCarburant;
        $SumSolde   = $SumAvance - $SumDepense;
        //$sommeDepense = [];
        //$sommeFm = [];
        $missions=[];

        foreach ($paginator as $value) {
            $mis = $value->getId();

            $mntDepense = $manager->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($mis);
            $mntFm = $manager->getRepository('AppBundle:FraisMission')->getMontantFmMissions($mis);
            $mntCarburant = $manager->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($mis);

            $depense = $mntDepense['total'] === null ? 0 : $mntDepense['total'];
            $fm = $mntFm['total'] === null ? 0 : $mntFm['total'];
            $carburant = $mntCarburant['total'] === null ? 0 : $mntCarburant['total'];

            $missions[]= array (
            "id"                => $value->getId(),
            "code"              => $value->getCode(),
            "depart"            => $value->getDepart(),
            "retour"            => $value->getRetour(),
            "vEmploye"          => $value->getVEmploye(),
            "vRollout"          => $value->getVRollout(),
            "vComptabilite"     => $value->getVComptabilite(),
            "avance"            => $value->getAvance(),
            "user"              => $value->getUser()->getNom(),
            "depense"           => $depense + $fm + $carburant,
            "solde"             => $value->getAvance() - $depense - $fm - $carburant,

            );
        }
       
        $forme=$form->createView();
        return $this->render('@App/Mission/mission.index.html.twig', array(
            'page'          => 'mission',
            'form'          => $form->createView(),
            'paginator'     => $paginator,
            'missions'      => $missions,
            'sumDepense'    => $SumDepense,
            'sumAvance'     => $SumAvance,
            'sumSolde'      => $SumSolde,
        ));
    }

     /**
     * @Route("/mission/employe/index",name="mission_employe_index")
     */
    public function missionEmployeIndexAction()
    {   
        $session = $this->get("session");
        $session->set("appel_employe","oui");
        $session->set("appel_journal","non");


        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $mission = $manager->getRepository('AppBundle:Mission')->getMissionUser($user,'M');
        //$mission = $mission->getQuery()->getResult();

        $qbAvance   = $manager->getRepository('AppBundle:Mission')->getAvanceMissionUser($user,'M');
        $qbDepense  = $manager->getRepository('AppBundle:DepenseMission')->getTotalDepenseMissions('M'); 
        $qbDepense  = $manager->getRepository('AppBundle:DepenseMission')->addFilterTotalDepenseMissions($qbDepense,null,null,null,$user,null,null,false);
        
        $qbFM       = $manager->getRepository('AppBundle:FraisMission')->getTotalFM('M');
        $qbFM       = $manager->getRepository('AppBundle:FraisMission')->addFilterTotalFM($qbFM,null,null,null,$user,null,null,false);

        $qbCarburant= $manager->getRepository('AppBundle:CarburantMission')->getTotalCarburantMissions('M');
        $qbCarburant= $manager->getRepository('AppBundle:CarburantMission')->addFilterTotalCarburantMissions($qbCarburant,null,null,null,$user,null,null,false);

       
        $SumAvance  = ($qbAvance[0]['total']    === null) ? 0 : $qbAvance[0]['total'] ;
        $SumDepense = ($qbDepense->getQuery()->getResult()[0]['total']   === null) ? 0 : $qbDepense->getQuery()->getResult()[0]['total'];
        $SumFM      = ($qbFM->getQuery()->getResult()[0]['total']        === null) ? 0 : $qbFM->getQuery()->getResult()[0]['total'];
        $SumCarburant= ($qbCarburant->getQuery()->getResult()[0]['total']        === null) ? 0 : $qbCarburant->getQuery()->getResult()[0]['total'];

        
        $SumSolde   = $SumAvance - $SumDepense - $SumFM - $SumCarburant;
        //$sommeDepense = [];
        //$sommeFm = [];
        //$SommeCarburant = [];
        $missions = [];
        foreach ($mission as $value) {
            $mis = $value->getId();
            $mntDepense = $manager->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($mis);
            $mntFm = $manager->getRepository('AppBundle:FraisMission')->getMontantFmMissions($mis);
            $mntCarburant = $manager->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($mis);

            $depense = $mntDepense['total'] === null ? 0 : $mntDepense['total'];
            $fm = $mntFm['total'] === null ? 0 : $mntFm['total'];
            $carburant = $mntCarburant['total'] === null ? 0 : $mntCarburant['total'];

            $missions[]= array (
            "id"                => $value->getId(),
            "code"              => $value->getCode(),
            "depart"            => $value->getDepart(),
            "retour"            => $value->getRetour(),
            "vEmploye"          => $value->getVEmploye(),
            "vRollout"          => $value->getVRollout(),
            "vComptabilite"     => $value->getVComptabilite(),
            "avance"            => $value->getAvance(),
            "user"              => $value->getUser()->getNom(),
            "depense"           => $depense,
            "fm"                => $fm,
            "carburant"         => $carburant,
            "total_depense"     => $depense + $fm + $carburant,
            "solde"             => $value->getAvance() - $depense - $fm - $carburant,

            );
        }

        

        ///////

        return $this->render('@App/Mission/mission.employe.index.html.twig', array(
            'page'          => 'mission',
            'missions'      => $missions,
            'sumAvance'     => $SumAvance,
            'sumDepense'    => $SumDepense,
            'sumFM'         => $SumFM,
            'sumpCarburant' => $SumCarburant,
            'sumSolde'      => $SumSolde,
        ));
    }
    /**
     * @Route("/index/note/frais",name="note_frais_index")
     */
    public function noteFraisIndexAction()
    {   
        $session = $this->get("session");
        $session->set("appel_employe","non");        
        $session->set("appel_journal","non");
        
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(NoteFraisFilterType::class);
        
        if (!is_null($response = $this->saveFilter($form, 'note_frais', 'note_frais_index'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Mission')->getMissionAll('C');
        $qbAvance   = $manager->getRepository('AppBundle:Mission')->getAvanceMission('C');
        $qbDepense  = $manager->getRepository('AppBundle:DepenseMission')->getTotalDepenseMissions('C');
        
        $paginator = $this->filter($form,'note_frais', $qb,$qbAvance,$qbDepense,null);
        //$pp = $this->filter($form, $qb, 'mission');
        
        $SumAvance  = ($qbAvance->getQuery()->getResult()[0]['total']    === null) ? 0 : $qbAvance->getQuery()->getResult()[0]['total'] ;
        $SumDepense = ($qbDepense->getQuery()->getResult()[0]['total']   === null) ? 0 : $qbDepense->getQuery()->getResult()[0]['total'];

        $SumSolde   = $SumAvance - $SumDepense;
        $sommeDepense = [];
        foreach ($paginator as $value) {
            $mis = $value->getId();
            $mntDepense = $manager->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($mis);
            
            $depense = $mntDepense['total'] === null ? 0 : $mntDepense['total'];
            
            $missions[]= array (
            "id"                => $value->getId(),
            "code"              => $value->getCode(),
            "depart"            => $value->getDepart(),
            "retour"            => $value->getRetour(),
            "vEmploye"          => $value->getVEmploye(),
            "vComptabilite"     => $value->getVComptabilite(),
            "avance"            => $value->getAvance(),
            "user"              => $value->getUser()->getNom(),
            "depense"           => $depense,
            "solde"             => $value->getAvance() - $depense,

            );
        }


        $forme=$form->createView();
        return $this->render('@App/Mission/note.frais.index.html.twig', array(
            'page'          => 'noteFrais',
            'form'          => $form->createView(),
            'paginator'     => $paginator,
            'missions'      => $missions,
            'sommeDepenses' => $sommeDepense,
            'sumDepense'    => $SumDepense,
            'sumAvance'     => $SumAvance,
            'sumSolde'      => $SumSolde,
        ));
    }
    /**
     * @Route("/index/note/frais/employe",name="note_frais_employe_index")
     */
    public function noteFraisEmployeIndexAction()
    {   
        $session = $this->get("session");
        $session->set("appel_employe","oui"); 
        $session->set("appel_journal","non");


        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $mission = $manager->getRepository('AppBundle:Mission')->getMissionUser($user,'C');
        //$mission = $mission->getQuery()->getResult();

        $qbAvance   = $manager->getRepository('AppBundle:Mission')->getAvanceMissionUser($user,'C');
        $qbDepense  = $manager->getRepository('AppBundle:DepenseMission')->getTotalDepenseMissions('C');
        $qbDepense  = $manager->getRepository('AppBundle:DepenseMission')->addFilterTotalDepenseMissions($qbDepense,null,null,null,$user,null,null,false);
        $SumAvance  = ($qbAvance[0]['total']    === null) ? 0 : $qbAvance[0]['total'] ;
        $SumDepense = ($qbDepense->getQuery()->getResult()[0]['total']   === null) ? 0 : $qbDepense->getQuery()->getResult()[0]['total'];

        $SumSolde   = $SumAvance - $SumDepense;
        $sommeDepense = [];
        $missions = [];
        foreach ($mission as $value) {
            $mis = $value->getId();
            $mntDepense = $manager->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($mis);
            
            $depense = $mntDepense['total'] === null ? 0 : $mntDepense['total'];
            
            $missions[]= array (
            "id"                => $value->getId(),
            "code"              => $value->getCode(),
            "depart"            => $value->getDepart(),
            "retour"            => $value->getRetour(),
            "vEmploye"          => $value->getVEmploye(),
            "vComptabilite"     => $value->getVComptabilite(),
            "avance"            => $value->getAvance(),
            "user"              => $value->getUser()->getNom(),
            "depense"           => $depense,
            "solde"             => $value->getAvance() - $depense,

            );
        }



        ///////

        return $this->render('@App/Mission/note.frais.employe.index.html.twig', array(
            'page'          => 'noteFrais',
            'missions'      => $missions,
            'sommeDepenses' => $sommeDepense,
            'sumAvance'     => $SumAvance,
            'sumDepense'    => $SumDepense,
            'sumSolde'      => $SumSolde,
        ));
    }

    /**
     * @Route("/new/mission",name="mission_new")
     */
    public function missionNewAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $mission = new Mission();
        $form = $this->createForm(MissionType::class, $mission);
        if ($form->handleRequest($request)->isValid())
        {
            //$annee = $form['annee']->getData();
            $annee = $request->request->get('mission')['annee'];
            $anneeMission = $mission->getDepart()->format('Y');
            /*if ($mission->getRetour() < $mission->getDepart())
            {
                $this->get('session')->getFlashBag()->add('danger', ' La date de départ doit être inférieur à la date de retour.');
            }else*/
            if( $annee != $anneeMission)
            {
                $this->get('session')->getFlashBag()->add('danger', " L'année [date de départ] doit être la même année du champ [année].");
            }else
            {
                $manager->persist($mission);
                $manager->flush();
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                $cryptage = $this->container->get('my.cryptage');
                return $this->redirect($this->generateUrl('mission_show', array('id' => $cryptage->my_encrypt($mission->getId()))));
            }
        }
        return $this->render('@App/Mission/mission.new.html.twig', array(
            'mission' => $mission,
            'form'    => $form->createView(),
        ));
    }
    /**
     * @Route("/new/notefrais",name="note_frais_new")
     */
    public function noteFraisNewAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $mission = new Mission();
        $form = $this->createForm(NoteFraisType::class, $mission);
        if ($form->handleRequest($request)->isValid())
        {
            //$annee = $form['annee']->getData();
            $annee = $request->request->get('note_frais')['annee'];
            $anneeMission = $mission->getDepart()->format('Y');
            /*if ($mission->getRetour() < $mission->getDepart())
            {
                $this->get('session')->getFlashBag()->add('danger', ' La date de départ doit être inférieur à la date de retour.');
            }else*/
            if( $annee != $anneeMission)
            {
                $this->get('session')->getFlashBag()->add('danger', " L'année [date de départ] doit être la même année du champ [année].");
            }else
            {
                $manager->persist($mission);
                $manager->flush();
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                $cryptage = $this->container->get('my.cryptage');
                return $this->redirect($this->generateUrl('note_frais_show', array('id' => $cryptage->my_encrypt($mission->getId()))));
            }
        }
        return $this->render('@App/Mission/note.frais.new.html.twig', array(
            'mission' => $mission,
            'form'    => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit/mission",name="mission_edit")
     * id : mission
     */
    public function missionEditAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $destination = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);

        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;


        $editForm = $this->createForm(MissionType::class, $mission, array(
            'action' => $this->generateUrl('mission_edit', array('id' => $cryptage->my_encrypt($mission->getId()))),
            'method' => 'PUT',
        ));
        
        if ($editForm->handleRequest($request)->isValid()) {
            $depart = $request->request->get('mission')['depart'];
            $retour = $request->request->get('mission')['retour'];
        
            $dd = $this->getDoctrine()->getRepository('AppBundle:Intervention')->valideDate($id,$depart,$retour);
            $d1 = $dd["d1"];
            $d2 = $dd["d2"];
           

            if (($d1 !== null) or ($d2 !== null)){
                if ($d1 != null){
                    $this->get('session')->getFlashBag()->add('danger', 'Date de départ doit être inférieur ou égale au : ' . $d1);
                }
                if ($d2 !== null){
                    $this->get('session')->getFlashBag()->add('danger', 'Date de retour doit être supérieur ou égale au : ' . $d2);
                }
            }else{
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                return $this->redirect($this->generateUrl('mission_edit', array('id' => $cryptage->my_encrypt($id))));
            }
        }
        return $this->render('@App/Mission/mission.edit.html.twig', array(
            'mission'               => $mission,
            'destination'           => $destination,
            'montantDepenseMission' => $montantDepenseMission,
            'montantCarburantMission' => $montantCarburantMission,
            'montantFmMission'      => $montantFmMission,
            'edit_form'             => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/avance/mission",name="mission_avance")
     * id : mission
     */
    public function missionAvanceAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $destination = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);
        
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);

        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;
       
        $avanceForm = $this->createForm(AvanceType::class, $mission, array(
            'action' => $this->generateUrl('mission_avance', array('id' => $cryptage->my_encrypt($mission->getId()))),
            'method' => 'PUT',
        ));
        
        
        if ($avanceForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('mission_show', array('id' => $cryptage->my_encrypt($id))));
          }
        return $this->render('@App/Mission/mission.avance.html.twig', array(
            'mission'               => $mission,
            'destination'           => $destination,
            'montantDepenseMission' => $montantDepenseMission,
            'montantCarburantMission' => $montantCarburantMission,
            'montantFmMission'      =>$montantFmMission,
            'avance_form'           => $avanceForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/show/mission",name="mission_show")
     * id : mission
     */
    public function missionShowAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $destination = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);

        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        

        $deleteForm = $this->createDeleteForm($id, 'mission_delete');
        return $this->render('@App/Mission/mission.show.html.twig', array(
            'mission'                   => $mission,
            'destination'               => $destination,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'montantFmMission'          => $montantFmMission,
            'delete_form'               => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/show/notefrais",name="note_frais_show")
     * id : mission
     */
    public function noteFraisShowAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $destination = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);

        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        //$montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        //$montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        

        $deleteForm = $this->createDeleteForm($id, 'mission_delete');
        return $this->render('@App/Mission/mission.show.html.twig', array(
            'mission'                   => $mission,
            'destination'               => $destination,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantCarburantMission'   => $montantCarburantMission,
            //'montantFmMission'          => $montantFmMission,
            'delete_form'               => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/delete/mission",name="mission_delete")
     *
     */
    public function missionDeleteAction(Mission $mission, Request $request){
        $form = $this->createDeleteForm($mission->getId(), 'mission_delete');
        if (strpos($mission->getCode(),'M') !== false){
            $m = 1;
        }else{
            $m = 0;
        }
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            if ($mission->getAvance() > 0)
            {   $id = $mission->getId();
            
                if (strpos($mission->getCode(),'M') !== false){
                    $this->get('session')->getFlashBag()->add('danger', "Impossible de supprimer une mission avec avance. Veuillez contacté le service comptablité pour effacer l'avance sur mission.");
                     $cryptage = $this->container->get('my.cryptage'); 
                }else{
                    $this->get('session')->getFlashBag()->add('danger', "Impossible de supprimer une node de frais avec avance. Veuillez contacté le service comptablité pour effacer l'avance sur note de frais.");
                    $cryptage = $this->container->get('my.cryptage'); 
                }
                $id = $cryptage->my_encrypt($id);
                if ($m == 1){
                    return $this->redirect($this->generateUrl('mission_show', array('id' => $id)));
                }else{
                    return $this->redirect($this->generateUrl('note_frais_show', array('id' => $id)));
                }
                //return $this->redirect($this->generateUrl('mission_show', array('id' => $id)));
            }else
            {
                $manager->remove($mission);
                try {
                $manager->flush();
                } catch(\Doctrine\DBAL\DBALException $e) {
                    $id = $mission->getId();
                    if (strpos($mission->getCode(),'M') !== false){
                        $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette mission,elle possede des interventions ou des dépenses ou des frais de mission.');
                        $cryptage = $this->container->get('my.cryptage');
                    }else{
                        $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette note de frais,elle possede des dépenses.');
                        $cryptage = $this->container->get('my.cryptage');
                    }
                    $id = $cryptage->my_encrypt($id);
                    if ($m == 1){
                        return $this->redirect($this->generateUrl('mission_show', array('id' => $id)));
                    }else{
                        return $this->redirect($this->generateUrl('note_frais_show', array('id' => $id)));
                    }
                    //return $this->redirect($this->generateUrl('mission_show', array('id' => $id)));
                }
            }
        }
        //return $this->redirect($this->generateUrl('mission_index'));
        if ($m == 1){
            return $this->redirect($this->generateUrl('mission_index'));
        }else{
            return $this->redirect($this->generateUrl('note_frais_index'));
        }
    }
    
    /**
     * @Route("/{id}/delete/mission_index",name="mission_delete_index",options = {"expose" = true})
     * id : mission
     */
   public function missionDelete2Action($id)
    {   
        $manager = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        if (strpos($mission->getCode(),'M') !== false){
            $m = 1;
        }else{
            $m = 0;
        }
        if ($mission->getAvance() > 0) 
        {
            if (strpos($mission->getCode(),'M') !== false){
                $this->get('session')->getFlashBag()->add('danger', "Impossible de supprimer une mission avec avance. Veuillez contacté le service comptablité pour effacer l'avance sur mission.");
            }else{
                $this->get('session')->getFlashBag()->add('danger', "Impossible de supprimer une note fe frais avec avance. Veuillez contacté le service comptablité pour effacer l'avance sur note de frais.");
            }
        }else
        {
            $manager->remove($mission);
            try {
            $manager->flush();
            } catch(\Doctrine\DBAL\DBALException $e) {
                if (strpos($mission->getCode(),'M') !== false){
                    $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette mission, elle possede des interventions ou des dépenses ou des frais de mission.');
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette note de frais, elle possede des dépenses.');
                }
            }
        }
        if ($m == 1){
            return $this->redirect($this->generateUrl('mission_index'));
        }else{
            return $this->redirect($this->generateUrl('note_frais_index'));
        }
    }
    /**
     * @Route("/{annee}/mission/next", name="mission_next",options = { "expose" = true })
     * 
     */
    public function missionNextAction($annee): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $code = $manager->getRepository("AppBundle:Mission")->getNextMission($annee);
        return $this->json(["code" =>$code],200);
    }
    /**
     * @Route("/{annee}/notefrais/next", name="note_frais_next",options = { "expose" = true })
     * 
     */
    public function missionNoteFraisNextAction($annee): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $code = $manager->getRepository("AppBundle:Mission")->getNextNoteFrais($annee);
        return $this->json(["code" =>$code],200);
    }
    /**
     * @Route("/{id}/mission_intervention",name="mission_intervention")
     * id : mission
     */
    public function missionInterventionAction($id){
        $cryptage = $this->container->get('my.cryptage'); 
        $id = $cryptage->my_decrypt($id);
        $mission                    = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);
        $interventions              = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getInterventions($id);
        $realisateurs               = $this->getDoctrine()->getRepository('AppBundle:InterventionUser')->getRealisateurs($id);
        
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);

        $montantDepenseMission      = ($montantDepenseMission['total']    === null) ? 0 : $montantDepenseMission['total'] ;
        $montantFmMission           = ($montantFmMission['total']    === null) ? 0 : $montantFmMission['total'] ;
        $montantCarburantMission    = ($montantCarburantMission['total']    === null) ? 0 : $montantCarburantMission['total'] ;
        
        return $this->render('@App/Mission/mission.intervention.html.twig', array(
            'page'                      => 'intervention',
            'mission'                   => $mission,
            'destination'               => $destination,
            'interventions'             => $interventions,
            'realisateurs'              => $realisateurs,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantFmMission'          => $montantFmMission,
            'montantCarburantMission'   => $montantCarburantMission,
        ));
    }
    /**
     * @Route("/{id}/mission_depense",name="mission_depense",options = { "expose" = true })
     * id: mission
     */
    public function missionDepenseAction($id,Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
 
        $depenseMission = new DepenseMission();
        $depenseMission->setMission($mission);
        $formDepense = $this->createForm(DepenseMissionType::class, $depenseMission, array(
            'action'        => $this->generateUrl('mission_depense', array('id' => $cryptage->my_encrypt($id))),
            'method'        => 'PUT',
        ));
        $formDepense->handleRequest($request); 
        if ($formDepense->isSubmitted()){   
            if ($formDepense->isValid()){
                $d = $depenseMission->getDateDep();
                $d1 = $mission->getDepart();
                $d2 = $mission->getRetour();
                if (($d>=$d1) and ($d<=$d2)){
                    $manager->persist($depenseMission);
                    $manager->flush();
                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    $depenseMission = new DepenseMission();
                    $depenseMission->setMission($mission);
                    $formDepense = $this->createForm(DepenseMissionType::class, $depenseMission, array(
                        'action' => $this->generateUrl('mission_depense', array('id' => $cryptage->my_encrypt($id))),
                        'method' => 'PUT',
                    ));
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date de depense de mission doit être entre: '. $d1->format('d/m/Y') .' et: ' . $d2->format('d/m/Y'));
                }
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'Erreur...');
            }
        }

        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);
        $depenses                   = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getDepenseMissions($id);
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        return $this->render('@App/Mission/mission.depense.html.twig', array(
            'page'                      => 'depense',
            'mission'                   => $mission,
            'destination'               => $destination,
            'depenses'                  => $depenses,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantFmMission'          => $montantFmMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'formDepense'               => $formDepense->createView()
        ));

    }
    /**
     * @Route("/{id}/dmedt",name="mission_depense_edit",options={"expose" = true})
     * id : depenseMission
     */
    public function missionDepenseEditAction($id,Request $request){
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        $depenseMission         = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->find($id);
        $mission                = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($depenseMission->getMission());

        $formDepense = $this->createForm(DepenseMissionType::class, $depenseMission, array(
            'action'        => $this->generateUrl('mission_depense_edit', array('id' => $cryptage->my_encrypt($id))),
            'method'        => 'PUT',
        ));
        $formDepense->handleRequest($request);
        if ($formDepense->isSubmitted()){   
            if ($formDepense->isValid()){
                $d = $depenseMission->getDateDep();
                $d1 = $mission->getDepart();
                $d2 = $mission->getRetour();
                if (($d>=$d1) and ($d<=$d2)){
                    $manager->persist($depenseMission);
                    $manager->flush();
                    //$depenses =$this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getDepenseMissions($id);
                    //$depenses = $depenses->getQuery()->getResult();
                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    return $this->redirect($this->generateUrl('mission_depense', array('id' => $cryptage->my_encrypt($mission->getId()))));
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date de depense de mission doit être entre: '. $d1->format('d/m/Y') .' et: ' . $d2->format('d/m/Y'));
                }
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'Erreur...');
            }
        }
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($depenseMission->getMission());
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($depenseMission->getMission());
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($depenseMission->getMission());
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($depenseMission->getMission());
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;


        return $this->render('@App/Mission/mission.depense.edit.html.twig', array(
            'mission'                   => $mission,
            'destination'               => $destination,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantFmMission'          => $montantFmMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'formDepense'               => $formDepense->createView()
        ));

    }

    /**
     * @Route("/{id}/mission_carburant",name="mission_carburant",options = { "expose" = true })
     * id: mission
     */
    public function missionCarburantAction($id,Request $request)
    {   
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);

        $carburantMission = new CarburantMission();
        $carburantMission->setMission($mission);
        $formCarburant = $this->createForm(CarburantMissionType::class, $carburantMission, array(
            'action'        => $this->generateUrl('mission_carburant', array('id' => $cryptage->my_encrypt($id))),
            'method'        => 'PUT',
        ));
        $formCarburant->handleRequest($request); 
        if ($formCarburant->isSubmitted()){   
            if ($formCarburant->isValid()){
                $d = $carburantMission->getDate();
                $d1 = $mission->getDepart();
                $d2 = $mission->getRetour();
                if (($d>=$d1) and ($d<=$d2)){
                    $manager->persist($carburantMission);
                    $manager->flush();
                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    $carburantMission = new CarburantMission();
                    $carburantMission->setMission($mission);
                    $formCarburant = $this->createForm(CarburantMissionType::class, $carburantMission, array(
                        'action' => $this->generateUrl('mission_carburant', array('id' => $cryptage->my_encrypt($id))),
                        'method' => 'PUT',
                    ));
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date de carburant de mission doit être entre: '. $d1->format('d/m/Y') .' et: ' . $d2->format('d/m/Y'));
                }
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'Erreur...');
            }
        }

        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);
        $carburants                 = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getCarburantMissions($id);
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        return $this->render('@App/Mission/mission.carburant.html.twig', array(
            'page'                      => 'carburant',
            'mission'                   => $mission,
            'destination'               => $destination,
            'carburants'                => $carburants,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'montantFmMission'          => $montantFmMission,
            'formCarburant'            => $formCarburant->createView()
        ));

    }

    /**
     * @Route("/{id}/cmedt",name="mission_carburant_edit",options={"expose" = true})
     * id : carburantMission
     */
    public function missionCarburantEditAction($id,Request $request){
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        $carburantMission         = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->find($id);
        $mission                = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($carburantMission->getMission());

        $formCarburant = $this->createForm(CarburantMissionType::class, $carburantMission, array(
            'action'        => $this->generateUrl('mission_carburant_edit', array('id' => $cryptage->my_encrypt($id))),
            'method'        => 'PUT',
        ));  
        $formCarburant->handleRequest($request);
        if ($formCarburant->isSubmitted()){   
            if ($formCarburant->isValid()){
                $d = $carburantMission->getDate();
                $d1 = $mission->getDepart();
                $d2 = $mission->getRetour();
                if (($d>=$d1) and ($d<=$d2)){
                    $manager->persist($carburantMission);
                    $manager->flush(); 
                    //$depenses =$this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getCarburantMissions($id);
                    //$depenses = $depenses->getQuery()->getResult();
                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    return $this->redirect($this->generateUrl('mission_carburant', array('id' => $cryptage->my_encrypt($mission->getId()))));
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date de depense de mission doit être entre: '. $d1->format('d/m/Y') .' et: ' . $d2->format('d/m/Y'));
                }
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'Erreur...');
            }
        }
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($carburantMission->getMission());
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($carburantMission->getMission());
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($carburantMission->getMission());
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($carburantMission->getMission());
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        return $this->render('@App/Mission/mission.carburant.edit.html.twig', array(
            'mission'                   => $mission,
            'destination'               => $destination,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantFmMission'          => $montantFmMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'formCarburant'             => $formCarburant->createView()
        ));

    }
    /**
     * @Route("/{id}/missionCarburantDelete",name="mission_carburant_delete",options = { "expose" = true })
     * id : carburantMission
     */
    public function missionCarburantDeleteAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $carburantMission = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->find($id);
        $mission = $carburantMission->getMission();
        
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($carburantMission);
        $manager->flush();
        $this->get('session')->getFlashBag()->add('success', 'Suppréssion effectuer avec sucées.');
        return $this->redirect($this->generateUrl('mission_carburant',array('id' => $cryptage->my_encrypt($mission->getId()))));

    }

    /**
     * @Route("/{id}/mission_fm",name="mission_fm",options = { "expose" = true })
     * id: mission
     */
    public function missionFmAction($id,Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        
        $fraisMission = new FraisMission();
        $fraisMission->setMission($mission);
        $formFm = $this->createForm(FraisMissionType::class, $fraisMission, array(
            'action'        => $this->generateUrl('mission_fm', array('id' => $cryptage->my_encrypt($id))),
            'method'        => 'PUT',
        ));
        $formFm->handleRequest($request);
        if ($formFm->isSubmitted()){   
            if ($formFm->isValid()){
                $d = $fraisMission->getDateFm();
                $d1 = $mission->getDepart();
                $d2 = $mission->getRetour();
                if (($d>=$d1) and ($d<=$d2)){
                    $manager->persist($fraisMission);
                    $manager->flush();
                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    $fraisMission = new FraisMission();
                    $fraisMission->setMission($mission);
                    $formFm = $this->createForm(FraisMissionType::class, $fraisMission, array(
                        'action' => $this->generateUrl('mission_fm', array('id' => $cryptage->my_encrypt($id))),
                        'method' => 'PUT',
                    ));
                }else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date de frais de mission doit être entre: '. $d1->format('d/m/Y') .' et: ' . $d2->format('d/m/Y'));
                }  
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'Frais de mission saisie déja à cette date pour cet employe.');
            }
        }
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($id);
        $userInterventions          = $this->getDoctrine()->getRepository('AppBundle:InterventionUser')->getRealisateurs($id);
        $fm                         = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getFMs($id);
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        $employes = array();
        foreach ($userInterventions as $value) {
            $employes[] = ['dateFm' =>$value->getIntervention()->getDateIntervention() ,'user' =>$value->getUser()];
        }
        return $this->render('@App/Mission/mission.fm.html.twig', array(
            'page'                      => 'fm',
            'mission'                   => $mission,
            'fms'                       => $fm,
            'destination'               => $destination,
            'employes'                  => $employes,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'montantFmMission'          => $montantFmMission,
            'formFm'                    => $formFm->createView()
        ));

    }
    /**
     * @Route("/{id}/mission_fm_import",name="mission_fm_import",options = { "expose" = true })
     * id: mission
     */
    public function missionFmImportAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        //$mission         = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $userInterventions = $this->getDoctrine()->getRepository('AppBundle:InterventionUser')->getRealisateurs($id);
        
        foreach ($userInterventions as $userIntervention) {
            
            $d = $userIntervention->getIntervention()->getDateIntervention();
            $user =$userIntervention->getUser();
            //$wilaya = $userIntervention->getIntervention()->getSite()->getWilaya()->getNom();
            $montantFm = $userIntervention->getIntervention()->getSite()->getWilaya()->getMontantFm();
            
            $fm = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->findBy(['user'=>$user,'dateFm'=>$d]);
            if (count($fm) >0) {
                $fraisMission = $fm[0];
               
                if ( $montantFm > $fraisMission->getMontant()){
                    $val = $fraisMission->getMontant();
                    $val = number_format($val, 2, ',', ' ');
                    $fraisMission->setMontant($montantFm);
                    $fraisMission->setObs('### Modification auto Ancien montant: ' . $val .' DA ### '. $fraisMission->getObs());
                    $manager->persist($fraisMission);
                    $manager->flush();

                }
            }else{
               
                if ($montantFm >0 ){
                    $fraisMission = new FraisMission();
                    $fraisMission->setUser($user);
                    $fraisMission->setDateFm($d);
                    $fraisMission->setMission($userIntervention->getIntervention()->getMission());
                    $fraisMission->setMontant($montantFm);
                    $fraisMission->setObs('### Ajout auto ###');
                    $manager->persist($fraisMission);
                    $manager->flush();
                }

            }
            
        }
        
        
        return $this->redirect($this->generateUrl('mission_fm',array('id' => $cryptage->my_encrypt($id))));

    }
    /**
     * @Route("/{id}/fmedt",name="mission_fm_edit",options={"expose" = true})
     * id : FmMission
     */
    public function missionFmEditAction($id,Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $id = $cryptage->my_decrypt($id);
        $fmMission              = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->find($id);
        $mission                = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($fmMission->getMission());
        
        $formFm = $this->createForm(FraisMissionType::class, $fmMission, array(
            'action'        => $this->generateUrl('mission_fm_edit', array('id' => $cryptage->my_encrypt($id))),
            'method'        => 'POST',
        ));
        $formFm->handleRequest($request);
        if ($formFm->isSubmitted()){   
            if ($formFm->isValid()){
                $d = $fmMission->getDateFm();
                $d1 = $mission->getDepart();
                $d2 = $mission->getRetour();
                if (($d>=$d1) and ($d<=$d2)){
                    $manager->persist($fmMission);
                    $manager->flush();
                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    return $this->redirect($this->generateUrl('mission_fm', array('id' => $cryptage->my_encrypt($mission->getId()))));
                }
                else{
                    $this->get('session')->getFlashBag()->add('danger', 'Date de frais de mission doit être entre: '. $d1->format('d/m/Y') .' et: ' . $d2->format('d/m/Y'));
                }
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'Erreur...');
            }
        }
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($fmMission->getMission());
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($fmMission->getMission());
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($fmMission->getMission());
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($fmMission->getMission());

        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        return $this->render('@App/Mission/mission.fm.edit.html.twig', array(
            'mission'                   => $mission,
            'destination'               => $destination,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantCarburantMission'     => $montantCarburantMission,
            'montantFmMission'          => $montantFmMission,
            'formFm'                    => $formFm->createView()
        ));
    }
    /**
     * @Route("/{id}/missionDepenseDelete",name="mission_depense_delete",options = { "expose" = true })
     * id : depensemission
     */
    public function missionDepenseDeleteAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $depenseMission = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->find($id);
        $mission = $depenseMission->getMission();
        
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($depenseMission);
        $manager->flush();
        //$this->get('session')->getFlashBag()->add('success', 'Suppréssion effectuer avec sucées.');
        return $this->redirect($this->generateUrl('mission_depense',array('id' => $cryptage->my_encrypt($mission->getId()))));

    }
    /**
     * @Route("/{id}/missionFMDelete",name="mission_fm_delete",options = { "expose" = true })
     * id : FMmission
     */
    public function missionFMDeleteAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $fraisMission = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->find($id);
        $mission = $fraisMission->getMission();
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($fraisMission);
        $manager->flush();
        //$this->get('session')->getFlashBag()->add('success', 'Suppréssion effectuer avec sucées.');
        return $this->redirect($this->generateUrl('mission_fm',array('id' => $cryptage->my_encrypt($mission->getId()))));

    }
    /**
     * @Route("/{id}/interventionNew",name="mission_intervention_new")
     * id : mission
     */
    public function missionInterventionNewAction($id,Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $mission                    = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($mission->getId());
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);

        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        $intervention = new Intervention();
        $intervention->setMission($mission);
        $form_recherche_site = $this->createForm(SiteRechercheType::class);
        $form_recherche_prestation = $this->createForm(PrestationRechercheType::class);
        $form_intervention = $this->createForm(InterventionType::class, $intervention);
        $siteid = !empty($request->request->all())  ? $request->request->all()['intervention']['siteid'] : null;
        $prestationid = !empty($request->request->all())  ? $request->request->all()['intervention']['prestationid'] : null;

        if($siteid !== null)
        {   
            $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($siteid);
            $intervention->setSite($site);
        }
        if($prestationid !== null)
        {   
            $prestation = $this->getDoctrine()->getRepository('AppBundle:Prestation')->find($prestationid);
            $intervention->setPrestation($prestation);
        }
        if ($form_intervention->handleRequest($request)->isValid()) {
            $dd =  $mission->getDepart()->format('d/m/Y');
            if (($intervention->getDateIntervention()<$mission->getDepart()) or ($intervention->getDateIntervention() > $mission->getRetour()) ){
                $this->get('session')->getFlashBag()->add('danger', "Date d'intervention doit être supérieur  ou égale à la date de départ: ". $mission->getDepart()->format('d/m/Y') ." et inférieur ou égale à la de retour: ". $mission->getRetour()->format("d/m/Y").".");
            }else{
                $manager->persist($intervention);
                $manager->flush();
            
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                return $this->redirect($this->generateUrl('mission_intervention_show', array('id' => $cryptage->my_encrypt($intervention->getId()))));
            }
        }
        $deleteForm = $this->createDeleteForm($id, 'mission_intervention_delete');
        return $this->render('@App/Mission/mission.intervention.new.html.twig', array(
            'id'                            => $id,
            'intervention'                  => $intervention,
            'mission'                       => $mission,
            'destination'                   => $destination,
            'montantDepenseMission'         => $montantDepenseMission,
            'montantCarburantMission'       => $montantCarburantMission,
            'montantFmMission'              => $montantFmMission,
            'form_intervention'             => $form_intervention->createView(),
            'form_recherche_site'           => $form_recherche_site->createView(),
            'form_recherche_prestation'     => $form_recherche_prestation->createView(),
            'form_delete'                   => $deleteForm->createView(),
            
        ));

    }
    /**
     * @Route("/{id}/interventionShow",name="mission_intervention_show")
     * id : intervention
     */
    public function missionInterventionShowAction($id,Request $request)
    {
        //$manager = $this->getDoctrine()->getManager(); 
        //$session = $this->get("session");
        //$session->set("appel_mission","oui");    

        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $intervention           = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getIntervention($id);
        $realisateurs           = $this->getDoctrine()->getRepository('AppBundle:InterventionUser')->getRealisateursIntervention($id);
        $mission                = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($intervention->getMission());
        $destination            = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($intervention->getMission());
        $montantDepenseMission  = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($intervention->getMission());
        $montantCarburantMission  = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($intervention->getMission());
        $montantFmMission       = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($intervention->getMission());
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        $interventionUser = new InterventionUser(); 
        $form_realisateur = $this->createForm(InterventionUserType::class,$interventionUser,array('id'=>$intervention,'date'=>$intervention->getDateIntervention(),'mission'=>$mission));
        
        //$form_realisateur = $this->createForm(new InterventionUserType($intervention),$interventionUser);
        //$form = $this->createForm(new DescriptionArticleType($article->getId()), $descriptionArticle);
        $deleteForm = $this->createDeleteForm($id, 'mission_intervention_delete');
        return $this->render('@App/Mission/mission.intervention.show.html.twig', array(
            'id'                        => $id,
            'intervention'              => $intervention,
            'realisateurs'              => $realisateurs,
            'mission'                   => $mission,
            'destination'               => $destination,
            'montantDepenseMission'     => $montantDepenseMission,
            'montantCarburantMission'   => $montantCarburantMission,
            'montantFmMission'          => $montantFmMission,
            'delete_form'               => $deleteForm->createView(),
            'form_realisateur'          => $form_realisateur->createView(),
            
        ));

    }
    /**
     * @Route("/{id}/interventionEdit",name="mission_intervention_edit")
     * id : intervention
     */
    public function missionInterventionEditAction($id,Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $intervention = $this->getDoctrine()->getRepository('AppBundle:Intervention')->find($id);
        $cryptage = $this->container->get('my.cryptage');
        //$id = $cryptage->my_decrypt($id);
        //$mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        //$intervention = new Intervention();
        //$intervention->setMission($mission);
        $mission                    = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($intervention->getMission());
        $destination                = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($intervention->getMission());
        $montantDepenseMission      = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($id);
        $montantCarburantMission    = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($id);
        $montantFmMission           = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getMontantFmMissions($id);
        
        $montantDepenseMission      = $montantDepenseMission['total'] !== null ? $montantDepenseMission['total'] : 0;
        $montantCarburantMission    = $montantCarburantMission['total'] !== null ? $montantCarburantMission['total'] : 0;
        $montantFmMission           = $montantFmMission['total'] !== null  ? $montantFmMission['total'] : 0;

        $form_recherche_site = $this->createForm(SiteRechercheType::class);
        $form_recherche_prestation = $this->createForm(PrestationRechercheType::class);
        $form_intervention = $this->createForm(InterventionType::class, $intervention);
        $form_intervention->get('sitecode')->setData($intervention->getSite()->getCode());
        $form_intervention->get('sitenom')->setData($intervention->getSite()->getNom());
        $form_intervention->get('projet')->setData($intervention->getPrestation()->getProjet()->getNom());
        $form_intervention->get('prestationnom')->setData($intervention->getPrestation()->getNom());
        
        //$siteid = $request->request->get('intervention')['siteid'];
        $siteid = !empty($request->request->all())  ? $request->request->all()['intervention']['siteid'] : null;
        //$prestationid = $request->request->get('intervention')['prestationid'];
        $prestationid = !empty($request->request->all())  ? $request->request->all()['intervention']['prestationid'] : null;
        if(($siteid !== null) and ($siteid !==""))
        {   
            $site = $this->getDoctrine()->getRepository('AppBundle:Site')->find($siteid);
            $intervention->setSite($site);
        }
        if(($prestationid !== null) and ($prestationid !== ""))
        {   
            $prestation = $this->getDoctrine()->getRepository('AppBundle:Prestation')->find($prestationid);
            $intervention->setPrestation($prestation);
        }
        if ($form_intervention->handleRequest($request)->isValid()) {
            if (($intervention->getDateIntervention()<$intervention->getMission()->getDepart()) or ($intervention->getDateIntervention() > $intervention->getMission()->getRetour()) ){
                $this->get('session')->getFlashBag()->add('danger', "Date d'intervention doit être supérieur  ou égale à la date de départ: ".$intervention->getMission()->getDepart()->format("d/m/Y")." et inférieur ou égale à la de retour: ".$intervention->getMission()->getRetour()->format("d/m/Y").".");
            }else{
                $manager->persist($intervention);
                $manager->flush();
            
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                //return $this->redirect($this->generateUrl('mission_intervention_show', array('id' => $cryptage->my_encrypt($intervention->getId()))));
            }
        }
        //$deleteForm = $this->createDeleteForm($id, 'mission_intervention_delete');
        return $this->render('@App/Mission/mission.intervention.edit.html.twig', array(
            //'id'                            => $id,
            'intervention'                  => $intervention,
            'mission'                       => $mission,
            'destination'                   => $destination,
            'montantDepenseMission'         => $montantDepenseMission,
            'montantCarburantMission'         => $montantCarburantMission,
            'montantFmMission'              => $montantFmMission,
            'form_intervention'             => $form_intervention->createView(),
            'form_recherche_site'           => $form_recherche_site->createView(),
            'form_recherche_prestation'     => $form_recherche_prestation->createView(),
            //'form_delete'                   => $deleteForm->createView(),
            
        ));
    }
    /**
     * @Route("/{id}/missionInterventionDelete/{param}",name="mission_intervention_delete",options = { "expose" = true })
     * id : intervention
     */
    public function missionInterventionDeleteAction(Intervention $intervention,$param=null, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $mission = $intervention->getMission();
        $manager->remove($intervention);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element, vous devez supprimer les réalisateurs en premiers.');
            $id = $intervention->getId();
            $id = $cryptage->my_encrypt($id);
            if ($param === null) {
                return $this->redirect($this->generateUrl('mission_intervention_show', array('id' => $id)));
            }else{
                return $this->redirect($this->generateUrl('mission_intervention',array('id' => $cryptage->my_encrypt($mission->getId()))));
            }

        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('mission_intervention',array('id' => $cryptage->my_encrypt($mission->getId()))));
        
    }
    /**
     * @Route("/{id}/interventionrealisateuradd",name="mission_intervention_realisateur_add",options = { "expose" = true })
     * id : intervention
     */
    public function missionInterventionRealisateurAddAction($id,request $request){
           
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $intervention = $manager->getRepository("AppBundle:Intervention")->find($id);
        $quest = $request->request->all()["intervention_user"]["User"];       
        foreach($quest as $realisateur){
            $user = $manager->getRepository("AppBundle:User")->find($realisateur);
            $interventionUser = new InterventionUser();
            $interventionUser->setUser($user);
            $interventionUser->setIntervention($intervention);
            
            $manager->persist($interventionUser);
            $manager->flush();
        }
        return $this->redirect($this->generateUrl('mission_intervention_show', array('id' => $cryptage->my_encrypt($id))));

    }
    /**
     * @Route("/{id}/deleteInterventionUser",name="intervention_realisateur_delete",options = {"expose" = true})
     */
    public function deleteInterventionUserAction($id){
        $manager = $this->getDoctrine()->getManager();
        $cryptage = $this->container->get('my.cryptage');
        $interventionUser = $manager->getRepository("AppBundle:InterventionUser")->find($id);
        $intervention = $interventionUser->getIntervention();
        $manager->remove($interventionUser);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            return $this->redirect($this->generateUrl('mission_intervention_show', array('id' => $cryptage->my_encrypt($intervention->getid()))));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('mission_intervention_show', array('id' => $cryptage->my_encrypt($intervention->getid()))));
    }
    /**
     * @Route("/m/v/e/{id}",name="mission_validation_employe",options = {"expose" = true})
     * id: mission
     */
    public function MissionValidationEmployeAction($id):Response
    {
        $manager = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $mission->setVEmploye(!$mission->getVEmploye());
        $manager->flush();
        if ($mission->getVEmploye() == true)
        {   $this->get('session')->getFlashBag()->add('success', 'Validation avec succès.');
        }else
        {
            $this->get('session')->getFlashBag()->add('success', 'Dévalider avec succès.');
        }
        return $this->json(["msg" =>''],200);
    }
    /**
     * @Route("/m/v/r/{id}",name="mission_validation_rollout",options = {"expose" = true})
     * id: mission
     */
    public function MissionValidationRolloutAction($id):Response
    {
        $manager = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $mission->setVRollout(!$mission->getVRollout());
        $manager->flush();
        if ($mission->getVRollout() == true)
        {   $this->get('session')->getFlashBag()->add('success', 'Validation avec succès.');
        }else
        {
            $this->get('session')->getFlashBag()->add('success', 'Dévalider avec succès.');
        }
        return $this->json(["msg" =>''],200);
    }
    /**
     * @Route("/m/v/c/{id}",name="mission_validation_comptabilite",options = {"expose" = true})
     * id: mission
     */
    public function MissionValidationComptabiliteAction($id):Response
    {
        $manager = $this->getDoctrine()->getManager();
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $mission->setVComptabilite(!$mission->getVComptabilite());
        $manager->flush();
        if ($mission->getVComptabilite() == true)
        {   $this->get('session')->getFlashBag()->add('success', 'Validation avec succès.');
        }else
        {
            $this->get('session')->getFlashBag()->add('success', 'Dévalider avec succès.');
        }
        return $this->json(["msg" =>''],200);
    } 

    
     /**
     * @Security("has_role('ROLE_COMPTABLE')")
     * @Route("/excel/note/frais/{id}",name="excel_note_frais",options = {"expose" = true})
     */
    
    public function excelNoteFraisAction($id){
        $user = $this->getUser();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);

        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        //$interventions = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getInterventions($mission);
        $depenses = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getDepenseMissions($mission);
        $carburants = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getCarburantMissions($mission);
        //$fms = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getFMs($mission,'nom');
        //$destinations = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($mission);
        
        //if ($destinations !== null){
        //    $destination = $destinations['destination'];
        //}
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        //$objPHPExcel->getDefaultStyle()->getFont()->setName('Bodoni MT');
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE NOTE DE FRAIS")
            ->setSubject("SNC RTIE NOTE DE FRAIS")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE NOTE DE FRAIS");
        //$dateTimeNow = time();
        $dateTimeNow = date('d/m/Y');

        $feuil = $objPHPExcel->getActiveSheet();
        $feuil->getDefaultStyle()
                ->applyFromArray(array(
                    'font'=>array(
                        'name' => 'Arial',
                        'size' => 12,
                        //'bold' => true
                    ),
                    'alignment'=>array(
                        //'horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'  =>\PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                    //'borders' => array(
                    //    'allborders'=>array(
                    //        'style' => \PHPExcel_Style_Border::BORDER_DASHDOT
                    //    )
                    //)
                ));
        $feuil->setCellValue('A2', 'Note de frais');
        $feuil->setCellValue('A3', 'Employe');
        //$feuil->setCellValue('A4', 'Desitination');
        $feuil->setCellValue('A4', 'Du');
        $feuil->setCellValue('A5', 'Au');
        $feuil->getStyle('A2:A5')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
        );
        
        $feuil->setCellValue('B2', $mission->getCode());
        $feuil->setCellValue('B3', $mission->getUser()->getNom());
        //$feuil->setCellValue('B4', $destination);
        $feuil->setCellValue('B4', date_format($mission->getDepart(),'d/m/Y'));
        $feuil->setCellValue('B5', date_format($mission->getRetour(),'d/m/Y'));
        
        $feuil = $objPHPExcel->getActiveSheet();
        $feuil->setCellValue('C2', 'Avance :');
        $feuil->setCellValue('C3', 'Dépense :');
        $feuil->setCellValue('C4', 'Carburant :');
        $feuil->setCellValue('C5', 'Solde :');
        $feuil->getStyle('C2:C5')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
        );
        $feuil->setCellValue('D2', $mission->getAvance());
        $feuil->setCellValue('D5', '=D2-D3-D4');
        $feuil->getStyle('D2:D5')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        $feuil->getStyle('D2:D5')->applyFromArray(
                array(
                    'borders' => array(
                        'horizontal'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        ),
                        'bottom'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        
        $i = 10;
        
        /** Liste des dépenses */
        $feuil->setCellValue('A'.$i, 'Dépenses');
        $feuil->mergeCells('A'.$i.':B'.$i);
        $i++;
        $j = $i;
        $feuil->setCellValue('A'.$i, 'Date');
        $feuil->setCellValue('B'.$i, 'Désignation');
        $feuil->setCellValue('C'.$i, 'Justification');
        $feuil->setCellValue('D'.$i, 'Montant');
        $feuil->getStyle('A'.($i-1).':D'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
            );
        
        $i++;
        $ddep = $i;
        
        foreach ($depenses as $depense) {
            $feuil->setCellValue('A'.$i, date_format($depense->getDateDep(),'d/m/Y'));
            $feuil->setCellValue('B'.$i, $depense->getDepense()->getNom());
            $feuil->setCellValue('C'.$i, $depense->getJustificationDepense()->getNom());
            $feuil->setCellValue('D'.$i, $depense->getMontant());
            $feuil->getStyle('D'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $i++;
        }

        $i--;
        $fdep = $i;

        $feuil->getStyle('A'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        $feuil->getStyle('D'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
            );
        
        $feuil->setCellValue('D3' , '=SUM(D'.$ddep.':C'.$fdep.')');
        //$feuil->setCellValue('D3', '=SUM(C'.$ddep.':C'.$fdep.')');
        //***** */
        /** Carburant */
        $i = $i +2;
        $feuil->setCellValue('A'.$i, 'Carburant');
        $feuil->mergeCells('A'.$i.':B'.$i);
        $i++;
        $j = $i;
        $feuil->setCellValue('A'.$i, 'Date');
        $feuil->setCellValue('B'.$i, 'Désignation');
        $feuil->setCellValue('C'.$i, 'Justification');
        $feuil->setCellValue('D'.$i, 'Montant');
        $feuil->getStyle('A'.($i-1).':D'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
            );
        
        $i++;
        $dcar = $i;
        
        foreach ($carburants as $carburant) {
            $feuil->setCellValue('A'.$i, date_format($carburant->getDate(),'d/m/Y'));
            $feuil->setCellValue('B'.$i, $carburant->getCarburant()->getNom());
            $feuil->setCellValue('C'.$i, $carburant->getJustificationDepense()->getNom());
            $feuil->setCellValue('D'.$i, $carburant->getMontant());
            $feuil->getStyle('D'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $i++;
        }

        $i--;
        $fcar = $i;

        $feuil->getStyle('A'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        $feuil->getStyle('D'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
            );
        
        $feuil->setCellValue('D4' , '=SUM(D' . $dcar . ':C' . $fcar . ')');
        //$feuil->setCellValue('D3', '=SUM(D' . ($fdep + 1) .':C' . ($fcar + 1) .')');

        //***** */
        $i = $i +2;
        $feuil->setCellValue('A'.$i, 'Employé');
        $feuil->setCellValue('C'.$i, 'Service comptabilité');
        $feuil->getStyle('A'.$i.':C'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true,
                        'underline' => true,
                    )
                )
            );
        $feuil->getStyle('A'.$i)->applyFromArray(
                array(
                        'bottom'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
        $feuil->getStyle('C'.$i)->applyFromArray(
                array(
                        'bottom'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

        $feuil->getColumnDimension('A')->setWidth(15);
        $feuil->getColumnDimension('B')->setWidth(42);
        $feuil->getColumnDimension('C')->setWidth(15);
        $feuil->getColumnDimension('D')->setWidth(15);

        $feuil->getStyle('A1:D'.$i)->getAlignment()->setWrapText(true);
        $feuil->getStyle('A1:D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    )
                )
            );
       
        $feuil->getPageSetup()->setHorizontalCentered(true);

        $feuil->getHeaderFooter()->setOddHeader('&L&B&USNC RTIE &C&B&UNOTE DE FRAIS &R&B&U&P/&N');
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

        $feuil->getStyle('A1:D1')->applyFromArray(
                array(
                    'borders' => array(
                        'top' => array('style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    ),
                ),
            )
        );
       
        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.1);
        $feuil->getPageMargins()->setLeft(0.1);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setScale(90);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "NOTE_FRAIS_SNC_RTIE_" . $mission->getCode() . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;


    }
    
    /**
     * @Security("has_role('ROLE_COMPTABLE')")
     * @Route("/excel/mission/{id}",name="excel_mission",options = {"expose" = true})
     */    
    public function excelMissionAction($id){
        $user = $this->getUser();
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);

        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')->find($id);
        $interventions = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getInterventions($mission);
        $depenses = $this->getDoctrine()->getRepository('AppBundle:DepenseMission')->getDepenseMissions($mission);
        $carburants = $this->getDoctrine()->getRepository('AppBundle:CarburantMission')->getCarburantMissions($mission);
        $fms = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->getFMs($mission,'nom');
        $destinations = $this->getDoctrine()->getRepository('AppBundle:Intervention')->getDestination($mission);
        
        if ($destinations !== null){
            $destination = $destinations['destination'];
        }
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        //$objPHPExcel->getDefaultStyle()->getFont()->setName('Bodoni MT');
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE Missions")
            ->setSubject("SNC RTIE Mission")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE Missions");
        //$dateTimeNow = time();
        $dateTimeNow = date('d/m/Y');


        $feuil = $objPHPExcel->getActiveSheet();
        $feuil->getDefaultStyle()
                ->applyFromArray(array(
                    'font'=>array(
                        'name' => 'Arial',
                        'size' => 12,
                        //'bold' => true
                    ),
                    'alignment'=>array(
                        //'horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'  =>\PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                    //'borders' => array(
                    //    'allborders'=>array(
                    //        'style' => \PHPExcel_Style_Border::BORDER_DASHDOT
                    //    )
                    //)
                ));
        $feuil->setCellValue('A2', 'Mission');
        $feuil->setCellValue('A3', 'Chef mission');
        $feuil->setCellValue('A4', 'Desitination');
        $feuil->setCellValue('A5', 'Départ');
        $feuil->setCellValue('A6', 'Retour');
        $feuil->getStyle('A2:A6')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
        );
        
        $feuil->setCellValue('B2', $mission->getCode());
        $feuil->setCellValue('B3', $mission->getUser()->getNom());
        $feuil->setCellValue('B4', $destination);
        //\PHPExcel_Shared_Date::PHPToExcel($depart)
        $feuil->setCellValue('B5', date_format($mission->getDepart(),'d/m/Y'));
        $feuil->setCellValue('B6', date_format($mission->getRetour(),'d/m/Y'));
        $feuil = $objPHPExcel->getActiveSheet();
        
        $feuil->setCellValue('C2', 'Avance :');
        $feuil->setCellValue('C3', 'Dépense :');
        $feuil->setCellValue('C4', 'Solde :');
        $feuil->getStyle('C2:C4')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
        );
        $feuil->setCellValue('D2', $mission->getAvance());
        $feuil->setCellValue('D4', '=D2-D3');
        $feuil->getStyle('D2:D4')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        $feuil->getStyle('D2:D4')->applyFromArray(
                array(
                    'borders' => array(
                        'horizontal'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        ),
                        'bottom'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        
        /** Liste des interventions */
        $i = 10;
        $feuil->setCellValue('A'.$i, 'Liste des interventions');
        $feuil->mergeCells('A'.$i.':B'.$i);
        $i++;
        $j = $i;
        $feuil->setCellValue('A'.$i, 'Date');
        $feuil->setCellValue('B'.$i, 'Désignation');
        $feuil->setCellValue('C'.$i, 'Site');
        $feuil->setCellValue('D'.$i, 'Wilaya');
        $feuil->getStyle('A'.($i-1).':D'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
            );
        
        $i++;
        
        foreach ($interventions as $intervention) {
            $feuil->setCellValue('A'.$i, date_format($intervention->getDateIntervention(),'d/m/Y'));
            $feuil->setCellValue('B'.$i, $intervention->getPrestation()->getNom());
            $feuil->setCellValue('C'.$i, $intervention->getSite()->getCode());
            $feuil->setCellValue('D'.$i, $intervention->getSite()->getWilaya()->getNom());

            $i++;
        }

        $i--;

        $feuil->getStyle('A'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        
        /** Liste des frais de mission */
        $i = $i+2;
        $feuil->setCellValue('A'.$i, 'Liste des frais de mission');
        $feuil->mergeCells('A'.$i.':B'.$i);
        $i++;
        $j = $i;
        $feuil->setCellValue('A'.$i, 'Date');
        $feuil->setCellValue('B'.$i, 'Nom');
        $feuil->setCellValue('C'.$i, 'Montant');
        $feuil->setCellValue('D'.$i, 'Emergement');
        $feuil->getStyle('A'.($i-1).':D'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
            );
        
        $i++;
        $k = $i;
        $dfm = $i;
        $usr = "";
        
        foreach ($fms as $fm) {
            $feuil->setCellValue('A'.$i, date_format($fm->getDateFm(),'d/m/Y'));
            $feuil->setCellValue('B'.$i, $fm->getUser()->getNom());
            $feuil->setCellValue('C'.$i, $fm->getMontant());
            $feuil->getStyle('C'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            if ($usr === ""){
                $usr =  $fm->getUser()->getId();
            }elseif ($usr !== $fm->getUser()->getId()) {
                $usr = $fm->getUser()->getId();
                $feuil->mergeCells('D'.$k.':D'.($i-1));
                $k = $i;
            }

            $i++;
        }

        if ($i !== $k){
            $feuil->mergeCells('D'.$k.':D'.($i-1));
        }

        $i--;
        $ffm = $i;
        $ffm = $i;
        if ($ffm < $dfm){
            $ffm = $dfm;
        }

        $feuil->getStyle('A'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        
        $feuil->getStyle('C'.$j.':C'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
            );

        /** Liste des dépenses */
        $i = $i+2;
        $feuil->setCellValue('A'.$i, 'Liste des dépenses');
        $feuil->mergeCells('A'.$i.':B'.$i);
        $i++;
        $j = $i;
        $feuil->setCellValue('A'.$i, 'Date');
        $feuil->setCellValue('B'.$i, 'Désignation');
        $feuil->setCellValue('C'.$i, 'Montant');
        $feuil->setCellValue('D'.$i, 'Justification');
        $feuil->getStyle('A'.($i-1).':D'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
            );
        
        $i++;
        $ddep = $i;
        
        foreach ($depenses as $depense) {
            $feuil->setCellValue('A'.$i, date_format($depense->getDateDep(),'d/m/Y'));
            $feuil->setCellValue('B'.$i, $depense->getDepense()->getNom());
            $feuil->setCellValue('C'.$i, $depense->getMontant());
            $feuil->setCellValue('D'.$i, $depense->getJustificationDepense()->getNom());
            $feuil->getStyle('C'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $i++;
        }

        $i--;
        $fdep = $i;
        if ($fdep < $ddep){
            $fdep = $ddep;
        }

        $feuil->getStyle('A'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        $feuil->getStyle('C'.$j.':C'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
            );
        
        //$feuil->setCellValue('D3', '=SUM(C'.$dfm.':C'.$ffm.')+SUM(C'.$ddep.':C'.$fdep.')');
        
        /** Liste carburant */
        $i = $i+2;
        $feuil->setCellValue('A'.$i, 'Carburant');
        $feuil->mergeCells('A'.$i.':B'.$i);
        $i++;
        $j = $i;
        $feuil->setCellValue('A'.$i, 'Date');
        $feuil->setCellValue('B'.$i, 'Désignation');
        $feuil->setCellValue('C'.$i, 'Montant');
        $feuil->setCellValue('D'.$i, 'Justification');
        $feuil->getStyle('A'.($i-1).':D'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    )
                )
            );
        
        $i++;
        $dcar = $i;
        $fcar = $i;
        if ($fcar < $dcar){
            $fcar = $dcar;
        }
        
        foreach ($carburants as $carburant) {
            $feuil->setCellValue('A'.$i, date_format($carburant->getDate(),'d/m/Y'));
            $feuil->setCellValue('B'.$i, $carburant->getCarburant()->getNom());
            $feuil->setCellValue('C'.$i, $carburant->getMontant());
            $feuil->setCellValue('D'.$i, $carburant->getJustificationDepense()->getNom());
            $feuil->getStyle('C'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $i++;
        }

        $i--;
        $fcar = $i;

        $feuil->getStyle('A'.$j.':D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'allborders'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
            );
        $feuil->getStyle('C'.$j.':C'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                )
            );
        
        $feuil->setCellValue('D3', '=SUM(C'.$dfm.':C'.$ffm.')+SUM(C'.$ddep.':C'.$fdep.')+SUM(C'.$dcar.':C'.$fcar.')');
        

        //******** */
        $i = $i +2;
        $feuil->setCellValue('A'.$i, 'Chef de mission');
        $feuil->setCellValue('C'.$i, 'Service comptabilité');
        $feuil->getStyle('A'.$i.':C'.$i)->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true,
                        'underline' => true,
                    )
                )
            );
        $feuil->getStyle('A'.$i)->applyFromArray(
                array(
                        'bottom'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
        $feuil->getStyle('C'.$i)->applyFromArray(
                array(
                        'bottom'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

        $feuil->getColumnDimension('A')->setWidth(15);
        $feuil->getColumnDimension('B')->setWidth(42);
        $feuil->getColumnDimension('C')->setWidth(15);
        $feuil->getColumnDimension('D')->setWidth(15);

        $feuil->getStyle('A1:D'.$i)->getAlignment()->setWrapText(true);
        $feuil->getStyle('A1:D'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    )
                )
            );
       
        $feuil->getPageSetup()->setHorizontalCentered(true);

        $feuil->getHeaderFooter()->setOddHeader('&L&B&USNC RTIE &C&B&UNOTE DE FRAIS DE MISSION &R&B&U&P/&N');
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

        $feuil->getStyle('A1:D1')->applyFromArray(
                array(
                    'borders' => array(
                        'top' => array('style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    ),
                ),
            )
        );
       
        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.1);
        $feuil->getPageMargins()->setLeft(0.1);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setScale(90);
        
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "Missions_SNC_RTIE_" . $mission->getCode() . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;


    }
    
    /**
     * @Security("has_role('ROLE_COMPTABLE')")
     * @Route("/excel/missions",name="excel_missions",options = { "expose" = true })
    */
   
    public function excelMissionsAction(){
                
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(MissionFilterType::class);
        
        /*if (!is_null($response = $this->saveFilter($form, 'mission', 'mission_index'))) {
            return $response;
        }*/
        $missions = $manager->getRepository('AppBundle:Mission')->getMissionAll('M');
        
        $missions = $this->filter($form,'mission', $missions,null,null,null,null,false);
        
        $code           = $form->get('code')->getData();
        $depart         = ($form->get('depart')->getData() !== null) ? $form->get('depart')->getData()['left_date'] : null;
        $retour         = ($form->get('depart')->getData() !== null) ? $form->get('depart')->getData()['right_date'] : null;
        $chef_mission   = $form->get('user')->getData();
        $vEmploye       = $form->get('vEmploye')->getData();
        $vRollout       = $form->get('vRollout')->getData();
        $vComptabilite  = $form->get('vComptabilite')->getData();
        
        $user = $this->getUser();
        
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE Journal des missions")
            ->setSubject("SNC RTIE Journal des mission")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE Journal des missions");
        $dateTimeNow = time();

        $feuil = $objPHPExcel->getActiveSheet();
        $feuil->setCellValue('A1', 'Mission RTIE');
        $feuil->getStyle('A1')->getFont()->setName('Candara');
        $feuil->getStyle('A1')->getFont()->setSize(20);
        $feuil->getStyle('A1')->getFont()->setBold(true);
        $feuil->getStyle('A1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $feuil->getStyle('A1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);
        $feuil->mergeCells('A1:B1');
        $feuil->getStyle('A1:I1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $feuil->getStyle('A1:I1')->getFill()->getStartColor()->setARGB('FF808080');
                $feuil->setCellValue('A3', 'Filtre');
        $feuil->getStyle('A3')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'outline'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
                )
        );
        $feuil->setCellValue('A4', 'Mission');
        $feuil->setCellValue('B4', 'Chet mission');
        $feuil->setCellValue('C4', 'Départ');
        $feuil->setCellValue('D4', 'Retour');
        $feuil->setCellValue('E4', 'V.Employe');
        $feuil->setCellValue('F4', 'V.Rollout');
        $feuil->setCellValue('G4', 'V.Compt.');

        $feuil->setCellValue('A5',($code === null ? 'Tous' : $code));
        $feuil->setCellValue('B5',($chef_mission === null ? 'Tous' : $chef_mission->getNom()));
        $feuil->setCellValue('C5',($depart === null ? 'Tous' : \PHPExcel_Shared_Date::PHPToExcel($depart)));
        $feuil->setCellValue('D5',($retour === null ? 'Tous' : \PHPExcel_Shared_Date::PHPToExcel($retour)));
        $feuil->setCellValue('E5',($vEmploye === null ? 'Tous' : ($vEmploye === 0 ? 'Non validé' : 'Validé')));
        $feuil->setCellValue('F5',($vRollout === null ? 'Tous' : ($vRollout === 0 ? 'Non validé' : 'Validé')));
        $feuil->setCellValue('G5',($vComptabilite === null ? 'Tous' : ($vComptabilite === 0 ? 'Non validé' : 'Validé')));

        $feuil->getStyle('A4:G4')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                    'borders' => array(
                        'top'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
                )
        );
        $feuil->getStyle('A4:A5')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $feuil->getStyle('B4:B5')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                )
        );
        $feuil->getStyle('E4:G5')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                )
        );
        $feuil->getStyle('G4')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $feuil->getStyle('A4:G4')->applyFromArray($styleThinBlackBorderOutline);
        $feuil->getStyle('A5:G5')->applyFromArray($styleThinBlackBorderOutline);

        $feuil->setCellValue('A7', 'Mission');
        $feuil->setCellValue('B7', 'Chet mission');
        $feuil->setCellValue('C7', 'Départ');
        $feuil->setCellValue('D7', 'Retour');
        $feuil->setCellValue('E7', 'Avance');
        $feuil->setCellValue('F7', 'Dépense');
        $feuil->setCellValue('G7', 'Frais mission');
        $feuil->setCellValue('H7', 'Carburant');
        $feuil->setCellValue('I7', 'Total Dépense');
        $feuil->setCellValue('J7', 'Solde');
        $feuil->getStyle('A7:J7')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                    'borders' => array(
                        'top'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
                )
        );

        $feuil->getStyle('A7')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $feuil->getStyle('B7')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                )
        );
        $feuil->getStyle('J7')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );

        $i = 8 ;
        foreach ($missions as $mission){
            $mntDep = $manager->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($mission->getId());
            $mntFm = $manager->getRepository('AppBundle:FraisMission')->getMontantFmMissions($mission->getId());
            $mntCar = $manager->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($mission->getId());
            $mntDep = $mntDep['total'] === null ? 0 : $mntDep['total'];
            $mntFm = $mntFm['total'] === null ? 0 : $mntFm['total'];
            $mntCar = $mntCar['total'] === null ? 0 : $mntCar['total'];
            $feuil
                ->setCellValue('A'.$i, $mission->getCode())
                ->setCellValue('B'.$i, $mission->getUser()->getnom())
                ->setCellValue('C'.$i, \PHPExcel_Shared_Date::PHPToExcel($mission->getDepart()))
                ->setCellValue('D'.$i, \PHPExcel_Shared_Date::PHPToExcel($mission->getRetour()))
                ->setCellValue('E'.$i, $mission->getAvance())
                ->setCellValue('F'.$i, $mntDep)
                ->setCellValue('G'.$i, $mntFm)
                ->setCellValue('H'.$i, $mntCar)
                ->setCellValue('I'.$i, '=SUM(F'.$i.':H'.$i.')')
                ->setCellValue('J'.$i, '=E'.$i.'-F'.$i.'-G'.$i.'-H'.$i)
                ;
            
                //->setCellValue('E'.$i, $i)
            
            $feuil
                ->getStyle('C'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
            $feuil
                ->getStyle('D'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy")
            ;
            $i++;
        }
        $i--;

        $feuil->getStyle('E8:J'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        

        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $feuil->getStyle('A8:J'.$i)->applyFromArray($styleThinBlackBorderOutline);

        
        $j = $i;
        $i = $i + 2;

        $feuil->setCellValue('E'.$i, '=SUM(E8:E'.$j.')');
        $feuil->setCellValue('F'.$i, '=SUM(F8:F'.$j.')');
        $feuil->setCellValue('G'.$i, '=SUM(G8:G'.$j.')');
        $feuil->setCellValue('H'.$i, '=SUM(H8:H'.$j.')');
        $feuil->setCellValue('I'.$i, '=SUM(I8:I'.$j.')');
        $feuil->setCellValue('J'.$i, '=SUM(J8:J'.$j.')');

        $feuil->getStyle('E'.$i.':J'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $feuil->getStyle('E'.$i.':J'.$i)->getFont()->setBold(true);
        $feuil->getStyle('E'.$i.':J'.$i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $styleThickBrownBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'FF993300'),
                ),
            ),
        );
        $feuil->getStyle('E'.$i.':j'.$i)->applyFromArray($styleThickBrownBorderOutline);

        
        
        $feuil->getColumnDimension('A')->setAutoSize(true);
        $feuil->getColumnDimension('B')->setAutoSize(true);
        $feuil->getColumnDimension('C')->setAutoSize(true);
        $feuil->getColumnDimension('D')->setAutoSize(true);
        $feuil->getColumnDimension('E')->setAutoSize(true);
        $feuil->getColumnDimension('F')->setAutoSize(true);
        $feuil->getColumnDimension('G')->setAutoSize(true);
        $feuil->getColumnDimension('H')->setAutoSize(true);
        $feuil->getColumnDimension('I')->setAutoSize(true);
        $feuil->getColumnDimension('J')->setAutoSize(true);

        $feuil->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.2);
        $feuil->getPageMargins()->setLeft(0.2);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

        
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "Journal_Des_Missions_SNC_RTIE_" . date('Y-m-d__H-i-s') . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response; 
    }
   
     /**
     * @Security("has_role('ROLE_COMPTABLE')") 
     * @Route("/excel/notes/frais",name="excel_notes_frais",options = { "expose" = true })
     */
    public function excelNotesFraisAction(){
        
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(MissionFilterType::class);
                
        /*if (!is_null($response = $this->saveFilter($form, 'mission', 'mission_index'))) {
            return $response;
        }*/
        $missions = $manager->getRepository('AppBundle:Mission')->getMissionAll('C');
        
        $missions = $this->filter($form,'note_frais', $missions,null,null,null,null,false);
        
        $code       = $form->get('code')->getData();
        $du         = ($form->get('depart')->getData() !== null) ? $form->get('depart')->getData()['left_date'] : null;
        $au         = ($form->get('depart')->getData() !== null) ? $form->get('depart')->getData()['right_date'] : null;
        $chef_mission   = $form->get('user')->getData();
        $vEmploye       = $form->get('vEmploye')->getData();
        $vComptabilite  = $form->get('vComptabilite')->getData();
        
        $user = $this->getUser();
        
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE note de frais")
            ->setSubject("SNC RTIE note de frais")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE note de frais");
        $dateTimeNow = time();

        $feuil = $objPHPExcel->getActiveSheet();
        $feuil->setCellValue('A1', 'NOTE DE FRAIS RTIE');
        $feuil->getStyle('A1')->getFont()->setName('Candara');
        $feuil->getStyle('A1')->getFont()->setSize(20);
        $feuil->getStyle('A1')->getFont()->setBold(true);
        $feuil->getStyle('A1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $feuil->getStyle('A1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);
        $feuil->getStyle('A1:G1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $feuil->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('FF808080');
        $feuil->mergeCells('A1:B1');
        $feuil->setCellValue('A3', 'Filtre');
        $feuil->getStyle('A3')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'outline'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
                )
        );
        $feuil->setCellValue('A4', 'Note de frais');
        $feuil->setCellValue('B4', 'Employe');
        $feuil->setCellValue('C4', 'Du');
        $feuil->setCellValue('D4', 'Au');
        $feuil->setCellValue('E4', 'V.Employe');
        $feuil->setCellValue('F4', 'V.Compt.');

        $feuil->setCellValue('A5',($code === null ? 'Tous' : $code));
        $feuil->setCellValue('B5',($chef_mission === null ? 'Tous' : $chef_mission->getNom()));
        $feuil->setCellValue('C5',($du === null ? 'Tous' : $au));
        $feuil->setCellValue('D5',($au === null ? 'Tous' : $au));
        $feuil->setCellValue('E5',($vEmploye === null ? 'Tous' : ($vEmploye === 0 ? 'Non validé' : 'Validé')));
        $feuil->setCellValue('F5',($vComptabilite === null ? 'Tous' : ($vComptabilite === 0 ? 'Non validé' : 'Validé')));

        $feuil->getStyle('A4:F4')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                    'borders' => array(
                        'top'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
                )
        );
        $feuil->getStyle('A4:A5')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $feuil->getStyle('B4:B5')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                )
        );
        $feuil->getStyle('E4:F5')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                )
        );
        $feuil->getStyle('F4')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $feuil->getStyle('A4:F4')->applyFromArray($styleThinBlackBorderOutline);
        $feuil->getStyle('A5:F5')->applyFromArray($styleThinBlackBorderOutline);

        $feuil->setCellValue('A7', 'Note de frais');
        $feuil->setCellValue('B7', 'Employé');
        $feuil->setCellValue('C7', 'Du');
        $feuil->setCellValue('D7', 'Au');
        $feuil->setCellValue('E7', 'Avance');
        $feuil->setCellValue('F7', 'Dépense');
        $feuil->setCellValue('G7', 'Carburant');
        $feuil->setCellValue('H7', 'Solde');
        $feuil->getStyle('A7:H7')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                    'borders' => array(
                        'top'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'argb' => 'FFA0A0A0'
                        ),
                        'endcolor'   => array(
                            'argb' => 'FFFFFFFF'
                        )
                    )
                )
        );

        $feuil->getStyle('A7')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $feuil->getStyle('B7')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                )
        );
        $feuil->getStyle('H7')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );

        $i = 8 ;
        foreach ($missions as $mission){
            $mntDep = $manager->getRepository('AppBundle:DepenseMission')->getMontantDepenseMissions($mission->getId());
            $mntDep = $mntDep['total'] === null ? 0 : $mntDep['total'];
            $mntCar = $manager->getRepository('AppBundle:CarburantMission')->getMontantCarburantMissions($mission->getId());
            $mntCar = $mntCar['total'] === null ? 0 : $mntCar['total'];

            $feuil
                ->setCellValue('A'.$i, $mission->getCode())
                ->setCellValue('B'.$i, $mission->getUser()->getnom())
                ->setCellValue('C'.$i, \PHPExcel_Shared_Date::PHPToExcel($mission->getDepart()))
                ->setCellValue('D'.$i, \PHPExcel_Shared_Date::PHPToExcel($mission->getRetour()))
                ->setCellValue('E'.$i, $mission->getAvance())
                ->setCellValue('F'.$i, $mntDep)
                ->setCellValue('G'.$i, $mntCar)
                ->setCellValue('H'.$i, '=E'.$i.'-F'.$i.'-G'.$i)
                ;
            $feuil
                ->getStyle('C'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
            $feuil
                ->getStyle('D'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy")
            ;
            $i++;
        }
        $i--;

        $feuil->getStyle('E8:H'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        

        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $feuil->getStyle('A8:H'.$i)->applyFromArray($styleThinBlackBorderOutline);

        
        $j = $i;
        $i = $i + 2;

        $feuil->setCellValue('E'.$i, '=SUM(E8:E'.$j.')');
        $feuil->setCellValue('F'.$i, '=SUM(F8:F'.$j.')');
        $feuil->setCellValue('G'.$i, '=SUM(G8:G'.$j.')');
        $feuil->setCellValue('H'.$i, '=SUM(H8:H'.$j.')');

        $feuil->getStyle('E'.$i.':H'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $feuil->getStyle('E'.$i.':H'.$i)->getFont()->setBold(true);
        $feuil->getStyle('E'.$i.':H'.$i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $styleThickBrownBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'FF993300'),
                ),
            ),
        );
        $feuil->getStyle('E'.$i.':H'.$i)->applyFromArray($styleThickBrownBorderOutline);

        
        
        $feuil->getColumnDimension('A')->setAutoSize(true);
        $feuil->getColumnDimension('B')->setAutoSize(true);
        $feuil->getColumnDimension('C')->setAutoSize(true);
        $feuil->getColumnDimension('D')->setAutoSize(true);
        $feuil->getColumnDimension('E')->setAutoSize(true);
        $feuil->getColumnDimension('F')->setAutoSize(true);
        $feuil->getColumnDimension('G')->setAutoSize(true);
        $feuil->getColumnDimension('H')->setAutoSize(true);

        $feuil->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.2);
        $feuil->getPageMargins()->setLeft(0.2);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);
        
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "Notes_De_Frais_SNC_RTIE_" . date('Y-m-d__H-i-s') . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response; 
    }

    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="mission_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('mission', $field, $type);
        return $this->redirect($this->generateUrl('mission_index'));
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
    protected function filter(FormInterface $form,$name, QueryBuilder $qb,$qbAvance = null,$qbDepense = null,$qbFM = null,$qbCarburant = null, $paginer = true){   
        // t = M : Mission
        // t = C : Note de grais
        $t = $name === 'mission' ? 'M' : 'C';
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) { 
                $code   = $form->get('code')->getData();
                $depart = ($form->get('depart')->getData() !== null) ? $form->get('depart')->getData()['left_date'] : null;
                $retour = ($form->get('depart')->getData() !== null) ? $form->get('depart')->getData()['right_date'] : null;
                $user   = $form->get('user')->getData();
                $vEmploye = $form->get('vEmploye')->getData();
                try {
                    $vRollout = $form->get('vRollout')->getData();
                } catch (\Throwable $th) {
                    $vRollout = null;
                }
                
                $vComptabilite = $form->get('vComptabilite')->getData();

                $manager = $this->getDoctrine()->getManager();
                // Recupere les sommes  avant pagination
                if ($qbDepense !== null){
                    $qbDepense = $manager->getRepository('AppBundle:DepenseMission')->addFilterTotalDepenseMissions($qbDepense,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);
                }
                if ($qbFM !== null ){
                    $qbFM = $manager->getRepository('AppBundle:FraisMission')->addFilterTotalFM($qbFM,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);
                }
                if ($qbCarburant !== null ){
                    $qbCarburant = $manager->getRepository('AppBundle:CarburantMission')->addFilterTotalCarburantMissions($qbCarburant,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);
                }

                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
                if ($qbAvance !== null ){
                    $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qbAvance);
                }
            }else{
                $code   = null;
                $depart = null;
                $retour = null;
                $user   = null;
                $vEmploye = null;
                $vRollout = null;
                $vComptabilite = null;

                $manager = $this->getDoctrine()->getManager();
                
                if ($qbDepense !== false){
                    $qbDepense = $manager->getRepository('AppBundle:DepenseMission')->addFilterTotalDepenseMissions($qbDepense,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);
                }
                if ($qbFM !== false ){
                    $qbFM = $manager->getRepository('AppBundle:FraisMission')->addFilterTotalFM($qbFM,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);            
                }
                if ($qbCarburant !== false ){
                    $qbCarburant = $manager->getRepository('AppBundle:CarburantMission')->addFilterTotalCarburantMissions($qbCarburant,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);            
                }
            }
        }else{
            $code   = null;
            $depart = null;
            $retour = null;
            $user   = null;
            $vEmploye = null;
            $vRollout = null;
            $vComptabilite = null;

            $manager = $this->getDoctrine()->getManager();
            
            if ($qbDepense !== null){
                $qbDepense = $manager->getRepository('AppBundle:DepenseMission')->addFilterTotalDepenseMissions($qbDepense,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);
            }
            if ($qbFM !== null ){
                $qbFM = $manager->getRepository('AppBundle:FraisMission')->addFilterTotalFM($qbFM,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);            
            }
            if ($qbCarburant !== null ){
                $qbCarburant = $manager->getRepository('AppBundle:CarburantMission')->addFilterTotalCarburantMissions($qbCarburant,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite,$t);            
            }
        }
        if ($paginer == true){
            $session = $this->get('session');
            $nbr_pages = $session->get("nbr_pages");
            if ($nbr_pages == null){
                $nbr_pages = 20;
            };        // nombre de ligne
            $this->addQueryBuilderSort($qb, $name);
            $request = $this->container->get('request_stack')->getCurrentRequest();
            return $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), $nbr_pages);
        }else{
            return $qb->getQuery()->getresult();
        }
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