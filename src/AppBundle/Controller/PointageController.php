<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FraisMission;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\PointageUser;
use AppBundle\Form\PointageAutoType;
use AppBundle\Form\PointageUserType;
use AppBundle\Form\PointageFilterType;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
* @Route("/pointage")
* @Security("has_role('ROLE_ROLLOUT')")
*/

class PointageController extends Controller
{
    /**
     * @Route("/new",name="pointage_new")
     */
    public function newAction(Request $request)
    {   $pointageUser = new PointageUser();
        $form = $this->createForm(PointageUserType::class, $pointageUser);
        $form->handleRequest($request); 
        if ($form->isSubmitted()){
            $manager = $this->getDoctrine()->getManager();
            $userv = $request->request->all()["appbundle_pointageuser"]["user"];
            $datev = $request->request->all()["appbundle_pointageuser"]["date"];
            $pointagev = $request->request->all()["appbundle_pointageuser"]["pointage"];
            $hTravailv = $request->request->all()["appbundle_pointageuser"]["hTravail"];
            $hRoutev = $request->request->all()["appbundle_pointageuser"]["hRoute"];
            $hSupv = $request->request->all()["appbundle_pointageuser"]["hSup"];
            $obsv = $request->request->all()["appbundle_pointageuser"]["obs"];
            foreach ($userv as $value) {
                $pointageUser =new PointageUser();
                $user = $manager->getRepository('AppBundle:User')->find($value);
                $pointage = $manager->getRepository('AppBundle:Pointage')->find($pointagev);
                $pointageUser   ->setUser($user)
                                ->setPointage($pointage)
                                ->setDate(new DateTime($datev))
                                ->setHTravail($hTravailv)
                                ->setHRoute($hRoutev)
                                ->setHSup($hSupv)
                                ->setObs($obsv)
                ;
                $manager->persist($pointageUser);
            }
            $manager->flush();
            return $this->redirect($this->generateUrl('pointage_index'));
        }
        return $this->render('@App/PointageUser/new.html.twig', array(
            'pointageUser' => $pointageUser,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/pointage_auto/{date}",name="pointage_auto",options = { "expose" = true })
     */
    public function pointageAutoAction($date,Request $request)
    {   
        $manager = $this->getDoctrine()->getManager();
        $interventions = $manager->getRepository('AppBundle:InterventionUser')->getInterventionsDate($date);
        $t = array();
        foreach ($interventions as $intervention) {
            $user = $intervention->getUser();
            $dist = $intervention->getIntervention()->getSite()->getWilaya()->getNom();
            if (array_key_exists($user->getId(), $t)) {
                if (strpos($t[$user->getId()],$dist) != false){
                    $t[$user->getId()] = $t[$user->getId()] . ", " . $dist;
                }
            }else{
                $t+=[$user->getId() => $dist];
            }
        }
        foreach ($t as $key => $value) {
            $user = $manager->getRepository('AppBundle:User')->find($key);
            $pointage = $manager->getRepository('AppBundle:Pointage')->find(1);//Mission
            $pointageUser = new PointageUser();
            $pointageUser->setUser($user);
            $pointageUser->setPointage($pointage);
            $pointageUser->setDate(new DateTime($date));
            $pointageUser->setObs($value);

            $validator = $this->get('validator');
            $errors = $validator->validate($pointageUser);

            if (count($errors) == 0) {
                $manager->persist($pointageUser);
                $manager->flush();
            }

        }
        
        return $this->redirect($this->generateUrl('pointage_index'));

    }

    /**
     * @Route("/{id}/pointage/user/delete",name="pointage_user_delete",options = { "expose" = true })
     */
    public function pointageUserDeleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $pointage = $manager->getRepository('AppBundle:PointageUser')->find($id);
        $manager->remove($pointage);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            /*$cryptage = $this->container->get('my.cryptage');
            $id = $pointage->getId();
            $id = $cryptage->my_encrypt($id);*/
        }

        return $this->redirect($this->generateUrl('pointage_index'));
    }

    /**
     * @Route("/index",name="pointage_index")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(PointageFilterType::class);
        $formAuto = $this->createForm(PointageAutoType::class);

        /*$editForm = $this->createForm(PointageUserType::class, $pointageUser, array(
            'action' => $this->generateUrl('pointage_edit', array('id' => $cryptage->my_encrypt($pointageUser->getId()))),
            'method' => 'PUT',
        ));*/

        if (!is_null($response = $this->saveFilter($form, 'pointage', 'pointage_index'))) {
            return $response;
        }
        //$qb = $manager->getRepository('AppBundle:PointageUser')->createQueryBuilder('p');
        if (is_null($values = $this->getFilter('pointage'))) {
            $qb = $manager->getRepository('AppBundle:PointageUser')->getPointages(1);
        }else{
            $qb = $manager->getRepository('AppBundle:PointageUser')->getPointages();
        } 
        $paginator = $this->filter($form, $qb, 'pointage');

        $date = ($form->get('date')->getData() !== null) ? $form->get('date')->getData()['left_date'] : null; 
        if ($date !== null){
            $nonPointer = $manager->getRepository('AppBundle:PointageUser')->nonPointer($date);
        }else{
            $nonPointer = null;
        }
        

        //$forme=$form->createView();
        return $this->render('@App/PointageUser/index.html.twig', array(
            'form'      => $form->createView(),
            'formAuto'  => $formAuto->createView(),
            'paginator' => $paginator,
            'nonPointer'=> $nonPointer,
            'jour'      => $date,
        
        ));
    }

    /**
     * @Route("/excel/pointage",name="excel_pointage",options = { "expose" = true })
     */
    public function excelPointageAction(){

        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(PointageFilterType::class);
        $qb = $manager->getRepository('AppBundle:PointageUser')->getPointages();        
        $pointages = $this->filter($form, $qb, 'pointage',false);

        $du = ($form->get('date')->getData() !== null) ? $form->get('date')->getData()['left_date'] : null;
        $au = ($form->get('date')->getData() !== null) ? $form->get('date')->getData()['right_date'] : null;
        $employe = ($form->get('user')->getData() !== null) ? $form->get('user')->getData()->getNom() : null;

        $fms = $manager->getRepository('AppBundle:FraisMission')->getFmsPeriode($du,$au);

        
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE Pointage")
            ->setSubject("SNC RTIE Journal Pointage")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE Pointage");
        $date = time();

        $feuil = $objPHPExcel->getActiveSheet();
        
        $feuil->setCellValue('A1', 'Journal RTIE');
       
        $feuil->mergeCells('A1:B1');
        $feuil->getStyle('A1')->getFont()->setName('Candara');
        $feuil->getStyle('A1')->getFont()->setSize(20);
        $feuil->getStyle('A1')->getFont()->setBold(true);
        $feuil->getStyle('A1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $feuil->getStyle('A1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);
        
        $feuil->mergeCells('C1:H1');
        $feuil->setCellValue('C1', 'Edite le : '. date('d/m/Y') . ' Par : ' . $user->getNom());
        $feuil->getStyle('C1')->getFont()->setName('Candara');
        $feuil->getStyle('C1')->getFont()->setSize(12);
        $feuil->getStyle('C1')->getFont()->setBold(true);
        $feuil->getStyle('C1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $feuil->getStyle('C1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);
        
        
        $feuil->getStyle('A1:F1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $feuil->getStyle('A1:F1')->getFill()->getStartColor()->setARGB('FF808080');
    
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
        $feuil->setCellValue('A4', 'Du');
        $feuil->setCellValue('A5', 'Au');
        $feuil->setCellValue('A6', 'Employé');

        $feuil->setCellValue('B4',($du === null ? 'Tous' : \PHPExcel_Shared_Date::PHPToExcel($du)));
        $feuil->setCellValue('B5',($au === null ? 'Tous' : \PHPExcel_Shared_Date::PHPToExcel($au)));
        $feuil->setCellValue('B6',($employe === null ? 'Tous' : $employe));
        $feuil->getStyle('B4')->getNumberFormat()->setFormatCode("dd/mm/yyyy");
        $feuil->getStyle('B5')->getNumberFormat()->setFormatCode("dd/mm/yyyy");

        $feuil->getStyle('A4:A6')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,

                    ),
                    /*'borders' => array(
                        'top'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),*/
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
        $feuil->getStyle('B4:B6')->applyFromArray(
            array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,

                ),
            )
        );
        /*$feuil->getStyle('A4:A5')->applyFromArray(
                array(
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );*/

        /*$feuil->getStyle('A4:A5')->applyFromArray(
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
        );*/

        /*$feuil->getStyle('F4')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );*/
       /* $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );*/

        $styleThinBlackBorderAllborders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $feuil->getStyle('A4:B6')->applyFromArray($styleThinBlackBorderAllborders);

        //$feuil->getStyle('A4:F4')->applyFromArray($styleThinBlackBorderOutline);
        //$feuil->getStyle('A5:F5')->applyFromArray($styleThinBlackBorderOutline);

        $feuil->setCellValue('A8', 'Date');
        $feuil->setCellValue('B8', 'Nom');
        $feuil->setCellValue('C8', 'FM');
        $feuil->setCellValue('D8', 'Obs');
        $feuil->setCellValue('E8', 'H.Travail ');
        $feuil->setCellValue('F8', 'H.Route');
        $feuil->setCellValue('G8', 'H.Sup');
        $feuil->setCellValue('H8', 'Designation');
        $feuil->getStyle('A8:H8')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
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
        
        $feuil->getStyle('A8')->applyFromArray(
                array(
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $feuil->getStyle('C8')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                )
        );
        $feuil->getStyle('H8')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );

        $i = 9 ;
        foreach ($pointages as $pointage){
            $feuil
                ->setCellValue('A'.$i, \PHPExcel_Shared_Date::PHPToExcel($pointage->getDate()))
                ->setCellValue('B'.$i, $pointage->getUser()->getnom())
                ->setCellValue('C'.$i, $this->getFms($fms,$pointage->getUser()->getId(),$pointage->getDate()))
                ->setCellValue('D'.$i, $pointage->getObs())
                ->setCellValue('E'.$i, $pointage->getHTravail())
                ->setCellValue('F'.$i, $pointage->getHRoute())
                ->setCellValue('G'.$i, $pointage->getHSup())
                ->setCellValue('H'.$i, $pointage->getPointage()->getDesignation())
                ;
            $feuil->getStyle('A'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
           
            $i++;
        }
        $i--;
        $styleThinBlackBorderAllborders = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $feuil->getStyle('C8:C'.$i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('A8:H'.$i)->applyFromArray($styleThinBlackBorderAllborders);
        
        $feuil->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,8);
        $feuil->getHeaderFooter()->setOddFooter('&P/&N');

        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.2);
        $feuil->getPageMargins()->setLeft(0.2);
        $feuil->getPageMargins()->setBottom(0.5);
        //$feuil->getPageSetup()->setFitToWidth(1);
        
        $feuil->getColumnDimension('A')->setAutoSize(true);
        $feuil->getColumnDimension('B')->setAutoSize(true);
        $feuil->getColumnDimension('C')->setAutoSize(true);
        $feuil->getColumnDimension('D')->setAutoSize(true);
        $feuil->getColumnDimension('E')->setAutoSize(true);
        $feuil->getColumnDimension('F')->setAutoSize(true);
        $feuil->getColumnDimension('G')->setAutoSize(true);
        $feuil->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->setAutoFilter("A8:H8");




        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "Pointage_SNC_RTIE_" . date('Y-m-d__H-i-s') . ".xlsx";
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
     * @Route("/{id}/edit",name="pointage_edit")
     * id : pointage
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
       $id = $cryptage->my_decrypt($id);
        $pointageUser = $this->getDoctrine()->getRepository('AppBundle:PointageUser')->find($id);
        $editForm = $this->createForm(PointageUserType::class, $pointageUser, array(
            'action' => $this->generateUrl('pointage_edit', array('id' => $cryptage->my_encrypt($pointageUser->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('pointage_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/PointageUser/edit.html.twig', array(
            'site'          => $pointageUser,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/pointage_users/{date}",name="pointage_users",options = { "expose" = true })
     */
    public function pointageUserAction($date)
    {
        $pointageUser = $this->getDoctrine()->getRepository('AppBundle:User')->getNotPointageUsers($date);
        $pointageUser = $pointageUser->getQuery()->getResult();
        $pointageUsers =  $this->get('serializer')->serialize($pointageUser, 'json');
        //return $interventions;
        return $this->json(["users"     => $pointageUsers,
                            ],
                            200);
    }

    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="pointage_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('pointage', $field, $type);
        return $this->redirect($this->generateUrl('pointage_index'));
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

    protected function getFms($fms,$userId,$date){
        foreach ($fms as $fm) {
            if (($userId == $fm->getUser()->getId()) && ($date == $fm->getDateFm())){
                return $fm->getMontant();
            }
        }
        return 0;
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
    protected function filter(FormInterface $form, QueryBuilder $qb, $name,$paginer = true)
    {   
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                //$this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
                $user       = $form->get('user')->getData();
                $du         = ($form->get('date')->getData() !== null) ? $form->get('date')->getData()['left_date'] : null;
                $au         = ($form->get('date')->getData() !== null) ? $form->get('date')->getData()['right_date'] : null;
                $manager    = $this->getDoctrine()->getManager();

                if ($qb !== null){
                    $qb = $manager->getRepository('AppBundle:PointageUser')->addFilterPointage($qb,$user,$du,$au);
                }
            }
        }
        // possible sorting
        // nombre de ligne
        if ($paginer == true){
            $session = $this->get('session');
            $nbr_pages = $session->get("nbr_pages");
            if ($nbr_pages == null){
                $nbr_pages = 20;
            };
            $this->addQueryBuilderSort($qb, $name);
            $request = $this->container->get('request_stack')->getCurrentRequest();
            return $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), $nbr_pages);
        }else{
            return $qb->getQuery()->getResult();
        }
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
