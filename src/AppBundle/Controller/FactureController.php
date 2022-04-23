<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Facture;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\FactureType;
use AppBundle\Form\FactureFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    /**
     * @Route("/excel/facture/{facture}", name="excel_facture")
     */
    public function excelFactureAction($facture)
    {
        $user = $this->getUser();
        $cryptage = $this->container->get('my.cryptage');
        $my_service = $this->container->get('app_bundle.my_service');
        //$dateTimeNow = date('d/m/Y');
        
        $facture = $cryptage->my_decrypt($facture);
        
        $facture = $this->getDoctrine()->getRepository('AppBundle:Facture')->find($facture);
        $interventions = $facture->getInterventions();
        $client = $facture->getBc()->getProjet()->getClientFacturation();
        $ets = $this->getDoctrine()->getRepository('AppBundle:Ets')->find(1);

        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();

        $objPHPExcel->getProperties()->setCreator($user->getNom())
            ->setLastModifiedBy("SNC RTIE")
            ->setTitle("SNC RTIE")
            ->setSubject("SNC RTIE")
            ->setDescription("SNC RTIE")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("SNC RTIE");
        
        // ##########################
        // Feuil ATTACHEMENT
        // ##########################

        
        $feuil = $objPHPExcel->getActiveSheet();
        $feuil->setTitle('Facture');
        
        // ##################
        // Mise en page
        // ##################

        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.1);
        $feuil->getPageMargins()->setLeft(0.1);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setScale(100);
        $feuil->getPageSetup()->setHorizontalCentered(true);
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 16);
        
        $feuil->getDefaultStyle()
                ->applyFromArray(array(
                    'font'=>array(
                        'name'  => 'Times New Roman',
                        'size'  => 10,
                        'color' =>  array('argb' => '000000'),
                    ),
                    'alignment'=>array(
                        'vertical'  =>\PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                ));
        


        // ##################
        // Logo RTIE
        // ##################

        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('logo');
        $objDrawing->setDescription('logo');
        $objDrawing->setPath('./images/logo.png');
        $objDrawing->setHeight(60);
        $objDrawing->setCoordinates('K1');
        $objDrawing->setOffsetX(10);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // ##################
        // Info RTIE
        // ##################

        $feuil  ->setCellValue('A2','Adresse')
                ->setCellValue('A3','RC N°')
                ->setCellValue('A4','N° ART')
                ->setCellValue('A5','N° NIF')
                ->setCellValue('A6','N° NIS')
                ->setCellValue('A7','Capital')
                ->setCellValue('A8','Tel/Fax')
                ->setCellValue('A9','Mobile')
                ->setCellValue('A10','Email')
                ->setCellValue('A11','RIB N°')
                ;
        $feuil  ->setCellValue('A1',$ets->getNom())
                ->setCellValue('B2',': ' . $ets->getAdresse())
                ->setCellValue('B3',': ' . $ets->getRc())
                ->setCellValue('B4',': ' . $ets->getArt())
                ->setCellValue('B5',': ' . $ets->getNif())
                ->setCellValue('B6',': ' . $ets->getNis())
                ->setCellValue('B7',': ' . $ets->getCapital() . ' DA' )
                ->setCellValue('B8',': ' . $ets->getTelephone())
                ->setCellValue('B9',': ' . $ets->getMobile())
                ->setCellValue('B10',': ' . $ets->getEmail())
                ->setCellValue('B11',': ' . $ets->getRib())
                ;
        $feuil->getStyle('A1:A11')->getFont()
                ->applyFromArray(array(
                    'bold'      =>true,
                ));
        $feuil->getStyle('B1:B11')->getFont()
                ->applyFromArray(array(
                    'bold'      =>true,
                    'size'      =>10,
                    'italic'    => true,
                ));
        
        // ##################
        // Info Client
        // ##################


        $feuil  ->setCellValue('I4', 'A')
                ->setCellValue('I5',$client->getNom())
                ->setCellValue('I6',$client->getAdresse())
                ->setCellValue('I8','Tel : ' . $client->getTelephone())
                ->setCellValue('I9','RC N : ' . $client->getRc())
                ->setCellValue('I10','TIN : ' . $client->getTin())
                ->setCellValue('I11','NIF : ' . $client->getNif())
                ;
        $feuil->mergeCells('I6:L7');
        $feuil->getStyle('I6')->getAlignment()->setWrapText(true);

        $feuil->getStyle('A1:L11')->getFont()
                ->applyFromArray(array(
                    'color' =>  array('argb' => '0000FF'),
                ));
                
        // ##################
        // Info Facture
        // ##################

        $feuil->setCellValue("A13", 'FACTURE N° ');
        $feuil->setCellValue("C13", ': ' . $facture->getNum());
        $feuil->setCellValue("A14", 'Date ');
        $feuil->setCellValue("C14", ': ' . date_format($facture->getDate(),'d/m/Y'));
        $feuil->getStyle("A13:A14")->getFont()->applyFromArray(array( "bold" => true, "size" => 12,'italic'=>true));
        $feuil->getStyle("C13:C14")->getFont()->applyFromArray(array( "bold" => true, "size" => 10,'italic'=>true));
        $feuil->mergeCells("A13:B13");
        $feuil->mergeCells("A14:B14");
        $feuil->mergeCells("C13:D13");
        $feuil->mergeCells("C14:D14");


        $feuil->setCellValue("I13", 'Réf ');
        $feuil->setCellValue("J13", ': ' . $facture->getBc()->getNum());
        $feuil->setCellValue("I14", 'Projet ');
        $feuil->setCellValue("J14", ': ' . $facture->getBc()->getProjet()->getNom());
        $feuil->getStyle("I13:I14")->getFont()->applyFromArray(array( "bold" => true, "size" => 12,'italic'=>true));
        $feuil->getStyle("J13:J14")->getFont()->applyFromArray(array( "bold" => true, "size" => 10,'italic'=>true));
        $feuil->mergeCells("J13:L13");
        $feuil->mergeCells("J14:L14");
        
        // ##################
        // Contenu de la Facture
        // ##################
        
        $i = 16;
        $feuil->setCellValue('A'.$i,'Site');
        $feuil->setCellValue('B'.$i,'PO');
        $feuil->setCellValue('C'.$i,'Region');
        $feuil->setCellValue('D'.$i,'Wilaya');
        $feuil->setCellValue('E'.$i,'Désignation');
        $feuil->setCellValue('J'.$i,'Prix Unité');
        $feuil->setCellValue('K'.$i,'Qte');
        $feuil->setCellValue('L'.$i,'Prix/HT DA');
        $feuil->mergeCells('E' . $i .':I' . $i);

        $feuil->getStyle('A' . $i . ':L' . $i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $feuil->getStyle('A' . $i . ':L' . $i)->getFill()->getStartColor()->setRGB('FFFF9A');
        $feuil->getStyle('A' . $i . ':L' . $i)->getFont()->applyFromArray(array( "bold" => true));

        $i++;
    
        foreach ($interventions as $intervention) {
            $feuil->setCellValue('A'.$i, $intervention->getSite()->getCode());
            $feuil->setCellValue('C'.$i, $intervention->getSite()->getWilaya()->getZone()->getNom());
            $feuil->setCellValue('D'.$i, $intervention->getSite()->getWilaya()->getNom());
            $feuil->setCellValue('E'.$i, $intervention->getPrestation()->getNom());
            $feuil->mergeCells('E' . $i .':I' . $i);
            $intervention->getPrestationBc() != null ? $feuil->setCellValue('J'.$i, $intervention->getPrestationBc()->getMontant()) : '0' ;
            $feuil->setCellValue('K'.$i, $intervention->getQuantite());
            $feuil->setCellValue('L'.$i, '=J'.$i . '* K' . $i);
            $i++;
        }     
        $feuil->getStyle('A16:L' . $i)->getAlignment()->setWrapText(true);
        $feuil->getStyle('J17:J' . ($i-1))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('L17:L' . ($i-1))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('A16:L' . ($i-1))->getBorders()->applyFromArray(
            array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        // Centrer la Zone / Région
        $feuil->getStyle('C16:C' . ($i-1))
            ->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            )
        );
        // Centrer la Quatité
        $feuil->getStyle('K16:K' . ($i-1))
            ->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            )
        );

        
        // ##################
        // Total Facture
        // ##################

        $feuil->mergeCells('A' . $i .':K' . $i);
        $feuil->setcellValue('A' . $i,'Sous Total');
        $feuil->getStyle('A' . $i)->getFont()->applyFromArray(array( "bold" => true, "size" => 12));
        $feuil->setcellValue('L' . $i,'=SUM(L17:L' . ($i-1) .')');
        $feuil->getStyle('L17:L' . $i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('A' . $i . ':K' . $i)->getFill()->getStartColor()->setRGB('8C8C8C');
        $feuil->getStyle('A' . $i . ':L' . $i)->getFont()->applyFromArray(array( "bold" => true));
        $feuil->getStyle('A' . $i . ':K' . $i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $feuil->getStyle('A' . $i . ':L' . $i)->getBorders()->applyFromArray(
            array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        $feuil->getStyle('A' . $i . ':L' . $i)
            ->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
            )
        );

        // ##################
        // TVA Total TTC Facture
        // ##################
        $i++;

        $feuil->mergeCells('J' . $i .':K' . $i);
        $feuil->mergeCells('J' . ($i+1) .':K' . ($i+1));
        $feuil->mergeCells('J' . ($i+2) .':K' . ($i+2));

        $feuil->setcellvalue('J' . $i,'TOTAL HT');
        $feuil->setcellvalue('J' . ($i+1),'TVA ' . $facture->getTva() . ' %');
        $feuil->setcellvalue('J' . ($i+2),'TOTAL TTC');

        $feuil->setcellvalue('L' . $i,'=L' . ($i-1));
        $feuil->setcellvalue('L' . ($i+1),'=L' . ($i-1) . '*' . $facture->getTva() . '/100');
        $feuil->setcellvalue('L' . ($i+2),'=SUM(L' .$i . ':L' . ($i+1).')');   
        
        $feuil->getStyle('L' . $i .':L' . ($i+2))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $feuil->getColumnDimension('L')->setWidth(15);

        $feuil->getRowDimension($i-1)->setRowHeight(25);
        $feuil->getRowDimension($i)->setRowHeight(25);
        $feuil->getRowDimension($i+1)->setRowHeight(25);
        $feuil->getRowDimension($i+2)->setRowHeight(25);

        $feuil->getStyle('J' . $i . ':L' . ($i+2))->getFont()->applyFromArray(array( "bold" => true));
        $feuil->getStyle('J' . $i . ':L' . ($i+2))
            ->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
            )
        );
        $feuil->getStyle('J' . $i . ':L' . ($i+2))->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $feuil->getStyle('J' . $i . ':L' . ($i+2))->getBorders()->applyFromArray(array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)));

        // Chiffre en lettre
        $nbr = $feuil->getCell( 'L'.($i+2) )->getCalculatedValue() ;

        $nbr = $my_service->NumberToLetter($nbr, "Dinnars", "Centimes");
        $feuil->setCellValue("A" . ($i+3),"Arrêtée la présente Facture à la somme de:");
        $feuil->getStyle('A' . ($i+3))->getFont()->applyFromArray(array( "bold" => true, "size" => 12));
        $feuil->setCellValue("A" . ($i+4),ucfirst($nbr));
        $feuil->getStyle('A' . ($i+4))->getFont()->applyFromArray(array( "bold" => true, "size" => 12,"italic"=>true));


        // ##########################
        // Feuil ATTACHEMENT
        // ##########################

        $objPHPExcel->createSheet();
        $feuil = $objPHPExcel->setActiveSheetIndex(1);
        $feuil->setTitle('Attachement');

        // ##################
        // Mise en page
        // ##################
        
        $feuil->getPageMargins()->setTop(0.5);
        $feuil->getPageMargins()->setRight(0.1);
        $feuil->getPageMargins()->setLeft(0.1);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setScale(100);
        $feuil->getPageSetup()->setHorizontalCentered(true);
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 17);
        
        $feuil->getDefaultStyle()
                ->applyFromArray(array(
                    'font'=>array(
                        'name'  => 'Arial',
                        'size'  => 10,
                        'color' =>  array('argb' => '000000'),
                    ),
                    'alignment'=>array(
                        'vertical'  =>\PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                ));
            
        // ##################
        // Largeur des Colonnes
        // ##################

        $feuil->getColumnDimension('A')->setWidth(19);
        $feuil->getColumnDimension('B')->setWidth(51);
        $feuil->getColumnDimension('C')->setWidth(14);
        $feuil->getColumnDimension('D')->setWidth(5);
        $feuil->getColumnDimension('E')->setWidth(20);

        // ##################
        // Hauteur des lignes
        // ##################

        $feuil->getRowDimension(1)->setRowHeight(6);
        $feuil->getRowDimension(2)->setRowHeight(30);
        $feuil->getRowDimension(5)->setRowHeight(3);
        $feuil->getRowDimension(7)->setRowHeight(3);
        $feuil->getRowDimension(9)->setRowHeight(3);
        $feuil->getRowDimension(10)->setRowHeight(3);
        $feuil->getRowDimension(12)->setRowHeight(3);

        // ##################
        // Les Bordures
        // ##################

        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    //'color' => array('argb' => 'FFFF0000'),
                ),
            ),
        );
        
        $feuil->getStyle('A1:E1')->applyFromArray($styleArray);
        $feuil->getStyle('A2:E5')->applyFromArray($styleArray);
        $feuil->getStyle('A6:E6')->applyFromArray($styleArray);
        $feuil->getStyle('A8:E9')->applyFromArray($styleArray);
        $feuil->getStyle('A11:E11')->applyFromArray($styleArray);
        $feuil->getStyle('A14:E16')->applyFromArray($styleArray);
        $feuil->getStyle('A1:E16')->applyFromArray($styleArray);

        // ##################
        // Entete d'attachement
        // ##################

        $feuil->mergeCells("A2:E2");
        $feuil->setCellValue("A2","ATTACHEMENT CONTRADICTOIRE\nProjet " . $facture->getBc()->getProjet()->getNom());
        $feuil->getStyle('A2')->getFont()->applyFromArray(array( "bold" => true, "size" => 12));
        $feuil->getStyle('A2')->applyFromArray(array('alignment'=>array('horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
        $feuil->getStyle("A2")->getAlignment()->setWrapText(true);

        $feuil->setCellValue("A3","Date d'émission: ");
        $feuil->getStyle("A3")->getFont()->applyFromArray(array( "bold" => true));

        $feuil->setCellValue("A4","FRLI001989-FP-B020300");
        $feuil->getStyle("A3")->getFont()->applyFromArray(array( "bold" => true,"underline"=>true));

        $feuil->setCellValue("A6","Affaire suivi par : " . $facture->getBc()->getResponsableBc()->getNom());
        $feuil->setCellValue("A11","Objet :  Se référer au  ");
        $feuil->setCellValue("B11",$facture->getBc()->getNum());
        $feuil->getStyle("A6:B11")->getFont()->applyFromArray(array( "bold" => true,"underline"=>true));

        $feuil->mergeCells("A14:E16");
        $feuil->getStyle("A14")->getAlignment()->setWrapText(true);
        $feuil->setCellValue("A14","IL A ÉTÉ PROCEDE CONTRADICTOIREMENT AU RELEVE DES OPERATIONS EXECUTEES PAR LE FOURNISSEUR  RTIE.  LES SIGNATAIRES CERTIFIENT L'EXACTITUDE DU RELEVE CONTRADICTOIRE DIT ATTACHEMENT POUR SERVIR ET VALOIR CE QUE DE DROIT.");
        $feuil->setCellValue("A17","Site");
        $feuil->setCellValue("B17","Désignation");
        $feuil->setCellValue("C17","PU DZD/T ");
        $feuil->setCellValue("D17","QTE");
        $feuil->setCellValue("E17","Montant DZD / HT ");
        $feuil->getStyle("A17:E17")->getFont()->applyFromArray(array( "bold" => true));
        $feuil->getStyle('A17:E17')->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            )
        );
        $feuil->getStyle('A17:E17')->getBorders()->applyFromArray(
            array(
            'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN)
            )
        );

        // ##################
        // Contenu de l'attachement
        // ##################
        
        $i = 18;

        $i++;
    
        foreach ($interventions as $intervention) {
            $feuil->setCellValue('A'.$i, $intervention->getSite()->getCode() . " " . $intervention->getSite()->getNom() );
            $feuil->setCellValue('B'.$i, $intervention->getPrestation()->getNom());
            $intervention->getPrestationBc() != null ? $feuil->setCellValue('C'.$i, $intervention->getPrestationBc()->getMontant()) : '0' ;
            $feuil->setCellValue('D'.$i, $intervention->getQuantite());
            $feuil->setCellValue('E'.$i, '=C'.$i . '* D' . $i);
            $i++;
        }     
        // Retour a la ligne pour le Nom du site et désignation
        $feuil->getStyle('A18:B' . $i)->getAlignment()->setWrapText(true);
        // Format millier Prix_U et Montant
        $feuil->getStyle('C18:C' . ($i-1))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('E17:E' . ($i-1))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        // Bordures
        $feuil->getStyle('A18:E' . ($i-1))->getBorders()->applyFromArray(
            array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        
        // Centrer la Quatité
        $feuil->getStyle('D18:D' . ($i-1))
            ->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            )
        );
        // Total
        $feuil->setCellValue("E" . $i,"=SUM(E18:E" . ($i-1) .")");
        $feuil->getStyle("E" . $i)->getFont()->applyFromArray(array( "bold" => true));
        $feuil->getStyle('E' . $i)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('E' . $i)->getBorders()->applyFromArray(
            array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        // Commentaires Client
        $i++;
        $feuil->mergeCells("A" . $i . ":B" . $i);
        $feuil->mergeCells("A" . ($i+1) . ":B" . ($i+1));
        $feuil->setCellValue("A" .  $i,"Commentaire :   POUR LA PARTIE ALCATEL LUCENT ALGERIE");
        $feuil->getStyle("A" . $i)->getFont()->applyFromArray(array( "bold" => true));
        //Commentaires Fournissier
        $feuil->mergeCells("C" . $i . ":E" . $i);
        $feuil->mergeCells("C" . ($i+1) . ":E" . ($i+1));
        $feuil->setCellValue("C" . $i,"POUR LE FOURNISSEUR");
        $feuil->getStyle("C" . $i)->getFont()->applyFromArray(array( "bold" => true));

        $i = $i + 2;
        $feuil->setCellValue("A" . $i,"Date"); 
        $feuil->setCellValue("A" . ($i+1),"Nom"); 
        $feuil->setCellValue("A" . ($i+2),"Fonction"); 
        $feuil->setCellValue("A" . ($i+3),"Signature \n et cachet ");
        $feuil->getRowDimension($i+3)->setRowHeight(30);
        // Bordures
        $feuil->getStyle('A' . $i . ":E" . ($i+3))->getBorders()->applyFromArray(
            array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );

        // ##########################
        // BC
        // ##########################

        $objPHPExcel->createSheet();
        $feuil = $objPHPExcel->setActiveSheetIndex(2);
        $feuil->setTitle('BC');

        // ##################
        // Mise en page
        // ##################
        
        $feuil->getPageMargins()->setTop(0.7);
        $feuil->getPageMargins()->setRight(0.1);
        $feuil->getPageMargins()->setLeft(0.1);
        $feuil->getPageMargins()->setBottom(0.5);
        $feuil->getPageSetup()->setScale(100);
        $feuil->getPageSetup()->setHorizontalCentered(true);
        $feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 17);
        
        $feuil->getDefaultStyle()
                ->applyFromArray(array(
                    'font'=>array(
                        'name'  => 'Arial',
                        'size'  => 10,
                        'color' =>  array('argb' => '000000'),
                    ),
                    'alignment'=>array(
                        'vertical'  =>\PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                ));
            
        // ##################
        // Largeur des Colonnes
        // ##################

        $feuil->getColumnDimension('A')->setWidth(19);
        $feuil->getColumnDimension('B')->setWidth(9);
        $feuil->getColumnDimension('C')->setWidth(27);
        $feuil->getColumnDimension('D')->setWidth(25);
        $feuil->getColumnDimension('E')->setWidth(16);

        // Fusionner le cellules
        $feuil->mergeCells("A1:A3");
        $feuil->mergeCells("B1:C3");

        // ##################
        // Les Bordures
        // ##################

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    //'color' => array('argb' => 'FFFF0000'),
                ),
            ),
        );
        $feuil->getStyle('A1:E3')->applyFromArray($styleArray);
        
        // Entete
        
        $feuil->getStyle("A1:E3")->getFont()->applyFromArray(array( "bold" => true));
        $feuil->setCellValue("A1","FRLI001989-FP-B020300");
        $feuil->setCellValue("B1",$facture->getBc()->getNum());
        $feuil->getStyle('B1')->getFont()->applyFromArray(array("size" => 12));
        $feuil->setCellValue("D1","Montant BO");
        $feuil->setCellValue("D2","Montant à ne pas dépasser");
        $feuil->setCellValue("D3","Montant global HT");
        $feuil->setCellValue("E1",$this->getDoctrine()->getRepository('AppBundle:PrestationBc')->getSomme($facture->getBc()));
        $feuil->setCellValue("E2","=E1-E3");
        $feuil->getStyle('E1:E3')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $feuil->getStyle('A1:B1')->applyFromArray(array(
            'alignment'=>array(
                'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        )
    );

        $feuil->getStyle("A1:B1")->getAlignment()->setWrapText(true);

        
        // Entete du tableau
        
        $feuil->setCellValue("A4","Date Fac");
        $feuil->setCellValue("B4","PO");
        $feuil->setCellValue("C4","N° ODS");
        $feuil->setCellValue("D4","Facture");
        $feuil->setCellValue("E4","Montants ");
        $feuil->getStyle("A4:E4")->getFont()->applyFromArray(array( "bold" => true));
        $feuil->getStyle('A4:E4')->applyFromArray(array(
                'alignment'=>array(
                    'horizontal'  =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            )
        );
        $feuil->getStyle('A4:E4')->getBorders()->applyFromArray(
            array(
            'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN)
            )
        );

        // ##################
        // Liste des factures de BC
        // ##################
        $factureBcs = $facture->getBc()->getFactures();
        
        $i = 5;
    
        foreach ($factureBcs as $factureBc) {
            $feuil->setCellValue('A'.$i, date_format($factureBc->getDate(),"d/m/Y"));
            $feuil->setCellValue('E'.$i, $this->getDoctrine()->getRepository('AppBundle:Intervention')->getSommeFacture($facture));
            $feuil->setCellValue('D'.$i, $factureBc->getNum());
            $i++;
        }  
        // total
        $feuil->setCellValue("E3","=SUM(E5:E" . ($i-1) . ")");  
        // Format millier Montant
        $feuil->getStyle('E5:E' . ($i-1))->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        // Bordures
        $feuil->getStyle('A5:E' . ($i-1))->getBorders()->applyFromArray(
            array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                )
            )
        );
        
        $feuil->getHeaderFooter()->setOddHeader('&C&B&USuivis Bon de commande');
        
        







        
        //$feuil->getHeaderFooter()->setOddHeader('&L&B&USNC RTIE &C&B&UNOTE DE FRAIS DE MISSION &R&B&U&P/&N');

        //$feuil->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

       /* $feuil->getStyle('A1:D1')->applyFromArray(
                array(
                    'borders' => array(
                        'top' => array('style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    ),
                ),
            )
        );*/
       
        
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $nom = "SNC_RTIE_Facture_" . $facture->getNum() . ".xlsx";
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$nom
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
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
