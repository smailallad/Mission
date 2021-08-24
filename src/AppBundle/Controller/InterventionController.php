<?php

namespace AppBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\JournalFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class InterventionController extends Controller
{   
    
    /**
     * @Route("journal", name="journal",options ={"expose" = true})
     */
    public function journalAction(){
        $session = $this->get("session");
        $session->set("appel_employe","non"); 
        $session->set("appel_journal","oui");
        
        $manager = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(JournalFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'journal', 'journal'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Intervention')->getInterventionsAll();
        //dump($qb);
        
        $paginator = $this->filter($form, $qb, 'journal');
        
        $forme=$form->createView();
        return $this->render('@App/Mission/journal.html.twig', array(
            'page'          => 'journal',
            'form'          => $form->createView(),
            'paginator'     => $paginator,
        ));
    }








//*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="journal_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('journal', $field, $type);
        return $this->redirect($this->generateUrl('journal'));
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
    protected function filter(FormInterface $form, QueryBuilder $qb, $name,$paginer = true){   
        $manager    = $this->getDoctrine()->getManager();
        $mission    = null;
        $code       = null;
        $site       = null;
        $du         = null;
        $au         = null;
        $user       = null;
        
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                
                $mission    = $form->get('mission')->getData();
                $code       = $form->get('code')->getData();
                $site       = $form->get('site')->getData();
                $du         = ($form->get('periode')->getData() !== null) ? $form->get('periode')->getData()['left_date'] : null;
                $au         = ($form->get('periode')->getData() !== null) ? $form->get('periode')->getData()['right_date'] : null;
                $user       = $form->get('user')->getData();
                //dump($user);
//                $qb         = $manager->getRepository('AppBundle:Intervention')->addFilterInterventionAll($qb,$mission,$code,$site,$du,$au,$user);
                //$this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
                
            }else{
            }
        }
        $qb = $manager->getRepository('AppBundle:Intervention')->addFilterInterventionAll($qb,$mission,$code,$site,$du,$au,$user);
        // possible sorting
        // nombre de ligne
        if ($paginer === true){
            $session = $this->get('session');
            $nbr_pages = $session->get("nbr_pages");

            if ($nbr_pages === null ){
                $nbr_pages = 20;
            }

            $this->addQueryBuilderSort($qb, $name);
            //dump($qb);
            $request = $this->container->get('request_stack')->getCurrentRequest();
            //dump($request);
            //dump($nbr_pages);
            //dump($request->query->get('page', 1));
            //dump($qb);
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
        //dump($qb);
        //dump($joints);
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

    /**
     * @Route("/excel/journal",name="excel_journal",options = { "expose" = true })
     */
    public function excelJournalAction(){

        $manager = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(JournalFilterType::class);
        //if (!is_null($response = $this->saveFilter($form, 'journal', 'journal'))) {
        //    return $response;
        //}
        $qb = $manager->getRepository('AppBundle:Intervention')->getInterventionsAll();
        //dump($qb);
        
        $interventions = $this->filter($form, $qb, 'journal',false);
        
        //dump($intervention);
        //$session = $this->get("session");
        //$session->set("appel_employe","non"); // appel = 1 non employe
        
        //$manager = $this->getDoctrine()->getManager();
        //$form = $this->createForm(MissionFilterType::class);
        //dump($form);
        //dump($form->get('user')->getData());
        //throw new \Exception("Arret");
        
       /* if (!is_null($response = $this->saveFilter($form, 'mission', 'mission_index'))) {
            return $response;
        }
        $missions = $manager->getRepository('AppBundle:Mission')->getMissionAll('M');

        $missions = $this->filter($form, $missions, 'mission',false);
        $missions = $missions->getQuery()->getResult();
        */
        $mission        = $form->get('mission')->getData();
        $code           = $form->get('code')->getData();
        $site           = $form->get('site')->getData();
        $du         = ($form->get('periode')->getData() !== null) ? $form->get('periode')->getData()['left_date'] : null;
        $au         = ($form->get('periode')->getData() !== null) ? $form->get('periode')->getData()['right_date'] : null;
        $chef_mission   = $form->get('user')->getData();
                
        $user = $this->getUser();
        
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        
        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE Journal de travail")
            ->setSubject("SNC RTIE Journal de travail")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE Journal de travail");
        $date = time();

        $feuil = $objPHPExcel->getActiveSheet();
        
        $feuil->setCellValue('A1', 'Journal RTIE');
       
        $feuil->mergeCells('A1:B1');
        $feuil->getStyle('A1')->getFont()->setName('Candara');
        $feuil->getStyle('A1')->getFont()->setSize(20);
        $feuil->getStyle('A1')->getFont()->setBold(true);
        $feuil->getStyle('A1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $feuil->getStyle('A1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);
        
        $feuil->mergeCells('C1:F1');
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
        $feuil->setCellValue('A4', 'Mission');
        $feuil->setCellValue('B4', 'Code site');
        $feuil->setCellValue('C4', 'Nom site');
        $feuil->setCellValue('D4', 'Chef mission');
        $feuil->setCellValue('E4', 'Du');
        $feuil->setCellValue('F4', 'Au');

        $feuil->setCellValue('A5',($mission === null ? 'Tous' : $mission . '*'));
        $feuil->setCellValue('B5',($code === null ? 'Tous' : $code . '*'));
        $feuil->setCellValue('C5',($site === null ? 'Tous' : $site . '*'));
        $feuil->setCellValue('D5',($chef_mission === null ? 'Tous' : $chef_mission->getNom()));
        $feuil->setCellValue('E5',($du === null ? 'Tous' : \PHPExcel_Shared_Date::PHPToExcel($du)));
        $feuil->setCellValue('F5',($au === null ? 'Tous' : \PHPExcel_Shared_Date::PHPToExcel($au)));
        $feuil->getStyle('E5')->getNumberFormat()->setFormatCode("dd/mm/yyyy");
        $feuil->getStyle('F5')->getNumberFormat()->setFormatCode("dd/mm/yyyy");

        $feuil->getStyle('A4:F4')->applyFromArray(
                array(
                    'font'    => array(
                        'bold'      => true
                    ),
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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
        /*$feuil->getStyle('A4:A5')->applyFromArray(
                array(
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );*/
        $feuil->getStyle('A4:D5')->applyFromArray(
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
        $feuil->getStyle('A4:F5')->applyFromArray($styleThinBlackBorderAllborders);

        //$feuil->getStyle('A4:F4')->applyFromArray($styleThinBlackBorderOutline);
        //$feuil->getStyle('A5:F5')->applyFromArray($styleThinBlackBorderOutline);

        $feuil->setCellValue('A7', 'Mission');
        $feuil->setCellValue('B7', 'Chef mission');
        $feuil->setCellValue('C7', 'Date');
        $feuil->setCellValue('D7', 'Code site');
        $feuil->setCellValue('E7', 'Nom site ');
        $feuil->setCellValue('F7', 'Wilaya');
        $feuil->setCellValue('G7', 'Intervention');
        $feuil->setCellValue('H7', 'Détail et anomalie');
        $feuil->setCellValue('I7', 'Réalisateur');
        $feuil->getStyle('A7:I7')->applyFromArray(
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
        
        $feuil->getStyle('A7')->applyFromArray(
                array(
                    'borders' => array(
                        'left'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );
        $feuil->getStyle('C7')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                )
        );
        $feuil->getStyle('I7')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );

        $i = 8 ;
        foreach ($interventions as $intervention){
            $feuil
                ->setCellValue('A'.$i, $intervention->getMission()->getCode())
                ->setCellValue('B'.$i, $intervention->getMission()->getUser()->getnom())
                ->setCellValue('C'.$i, \PHPExcel_Shared_Date::PHPToExcel($intervention->getDateIntervention()))
                ->setCellValue('D'.$i, $intervention->getSite()->getCode())
                ->setCellValue('E'.$i, $intervention->getSite()->getNom())
                ->setCellValue('F'.$i, $intervention->getSite()->getWilaya()->getNom())
                ->setCellValue('G'.$i, $intervention->getPrestation()->getNom())
                ->setCellValue('H'.$i, $intervention->getDesignation())
                ;
            $realisateurs = $this->getDoctrine()->getRepository('AppBundle:InterventionUser')->getRealisateursIntervention($intervention);
            //dump($realisateurs);
            //throw new \Exception("Error Processing Request", 1);
            
            $var = "";
            foreach ($realisateurs as $realisateur) {
                if (strlen($var)) {
                    $var = $var . ', ' . $realisateur->getUser()->getNom();
                }else{
                    $var = $realisateur->getUser()->getNom();
                }
            }
            $feuil->setCellValue('I'.$i, $var);
            
            $feuil->getStyle('C'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
           
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
        $feuil->getStyle('A7:I'.$i)->applyFromArray($styleThinBlackBorderAllborders);
        $feuil->getStyle('A8:T'.$i)->getAlignment()->setWrapText(true);
        $feuil->getStyle('A8:I'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                )
        );
        $feuil->freezePane('B8');
        
      /*  $styleThickBrownBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'FF993300'),
                ),
            ),
        );
        $feuil->getStyle('E'.$i.':I'.$i)->applyFromArray($styleThickBrownBorderOutline);
*/

        
        $feuil->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
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
        $feuil->getColumnDimension('G')->setWidth(50);
        $feuil->getColumnDimension('H')->setWidth(50);
        $feuil->getColumnDimension('I')->setAutoSize(true);
        
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "Journal_de_travail_SNC_RTIE_" . date('Y-m-d__H-i-s') . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response; 
    }
   



}


