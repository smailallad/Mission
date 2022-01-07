<?php

namespace AppBundle\Controller;

use DateTime;
use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Form\DemoType;
use AppBundle\Form\TestType;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\FonctionUser;
use AppBundle\Form\UserMPUserType;
use AppBundle\Model\ChangePassword;
use AppBundle\Form\ResetPasswordType;
//use DoctrineExtensions\Query\Mysql\Now;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $session = $this->get("session");
        $session->set("nbr_pages",20);
        $session->set("appel_employe",""); 
        $session->set("appel_mission",""); 
        $session->set("appel_journal","");

        $seuilAssurance=30;
        $seuilControlTech=30;

        $alertEntretiens = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getListeAlerteInterventions();
        //$vehicules = $this->getDoctrine()->getRepository('AppBundle:Assurance')->getListeAlerteAssurances($seuilAssurance);
        $assuranceDepassers = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getAssuranceDepasser();
        $assuranceAlertes = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getAssuranceAlerte($seuilAssurance);

        $controlTechDepassers = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getControlTechDepasser();
        $controlTechAlertes = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getControlTechAlerte($seuilAssurance);

        $sansAssurances = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getSansAssuarence();
        $sansControlTechniques = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getSansControlTechnique();
        
        $relevers = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->getRelever();

        $interventionImportantes = $this->getDoctrine()->getRepository('AppBundle:InterventionVehicule')->getInterventionImportantes();
        return $this->render("default/index.html.twig",array(
            'alertEntretiens'           => $alertEntretiens,
            'assuranceDepassers'        => $assuranceDepassers,
            'assuranceAlertes'          => $assuranceAlertes,
            'controlTechDepassers'      => $controlTechDepassers,
            'controlTechAlertes'        => $controlTechAlertes,
            'sansAssurances'            => $sansAssurances,
            'sansControlTechniques'     => $sansControlTechniques,
            'seuilAssurance'            => $seuilAssurance,
            'seuilControlTech'          => $seuilControlTech,
            'relevers'                  => $relevers,
            'interventionImportantes'   => $interventionImportantes
        ));
    }
    /**
     * @Route("/{id}/ump",name="updateMP").
     *
     */
    public function updateMPAction($id,Request $request){
        /*
        $cryptage = $this->container->get('my.cryptage');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($cryptage->my_decrypt($id));
        $passwordEncoder = $this->get('security.password_encoder');
        $form = $this->createForm(UserMPUserType::Class, $user, array(
            'action' => $this->generateUrl('updateMP', array('id' => $id)),
            'method' => 'PUT',
        ));
        if ($form->handleRequest($request)->isValid())
        {
            throw new \Exception("Error Processing Request", 1);
            
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            if ($form->isValid())
            {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Mot de passe changer avec succès.');
                return $this->redirectToRoute('homepage');
            }
        }else{
            //throw new \Exception("Arret");
        }
        return $this->render('@App/User/mpUser.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));*/

        //$this->denyAccessUnlessGranted('ROLE_USER');
        $cryptage = $this->container->get('my.cryptage');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($cryptage->my_decrypt($id));

        $em = $this->getDoctrine()->getManager();
        //$user = $this->getUser();
        $changePassword = new ChangePassword();
        // rattachement du formulaire avec la class changePassword
        $form = $this->createForm(ResetPasswordType::Class, $changePassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
 
            $newpwd = $form->get('Password')['first']->getData();
            $passwordEncoder = $this->get('security.password_encoder');
            $newEncodedPassword = $passwordEncoder->encodePassword($user, $newpwd);
            $user->setPassword($newEncodedPassword);
 
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe à bien été changé !');
 
            return $this->redirectToRoute('homepage');
        }
 
        return $this->render('@App/User/mpUser.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user
        ));
 
    }

    /**
     * @Route("test/{date}",name="test")
     */
    public function defaultAction(Request $request,$date=null)
    {/*
        $date = new DateTime(date("Y-m-d"));
        $fonctionUser = new FonctionUser();
        $fonction = $this->getDoctrine()->getRepository("AppBundle:Fonction")->find(1);
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->find(1);

        $fonctionUser->setDatefonction($date);
        $fonctionUser->setUser($user);
        $fonctionUser->setFonction($fonction);

        $this->getDoctrine()->getManager()->persist($fonctionUser);
        $this->getDoctrine()->getManager()->flush();

        return $this->render("@App/Default/test.html.twig", array(

        ));*/

       

        if ($date !==null)
        {
            $test = $this->getDoctrine()->getRepository("AppBundle:Test")->find($date);
        }else
        {
            $test = new Test();
        }
        $form = $this->createForm(TestType::Class, $test);
        //$tables = $this->getDoctrine()->getRepository("AppBundle:Test")->findAll();
        $tables = $this->getDoctrine()->getRepository("AppBundle:Test")->test('2000-01-01','2000-12-31');
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($test);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
        }

        
        return $this->render("@App/Default/test.html.twig", array(
            'form' => $form->createView(),
            'tables'   =>$tables
        ));


    }

    
    /**
     * @Route("test1",name="test1")
     */
    public function testAction(Request $request)
    {
        
        /*
        $date = new DateTime(date('2015-12-02'));
        $vehicule = $this->getDoctrine()->getRepository("AppBundle:Vehicule")->find(1);
        $kms1 = $this->getDoctrine()->getRepository("AppBundle:EntretienVehicule")->getPreview($date,$vehicule);
        $kms2 = $this->getDoctrine()->getRepository("AppBundle:EntretienVehicule")->getNext($date,$vehicule);

        
        
        */
        /*
        $date = new DateTime('2021-04-14');
        //679
        $mission = $this->getDoctrine()->getRepository("AppBundle:Mission")->find(679);
        $missions = $this->getDoctrine()->getRepository("AppBundle:User")->getChefMissionDate($date,$mission);
        
        */

        $req = $this->getDoctrine()->getRepository("AppBundle:Intervention")->getInterventionsAll();
        $req = $req->getQuery()->getResult();
        
        $var =$req[1];
        
        $var1 = $var->getInterventionUsers();
        
        $form = $this->createForm(DemoType::class);
        
        return $this->render('@App/Default/test.html.twig', array(
            'form'  => $form->createView(),
            'var'   => $var1
        ));
        

        //
        //$manager = $this->getDoctrine()->getManager();
        //$qb = $manager->getRepository('AppBundle:DepenseMission')->test();
        
        //throw new \Exception("Arret");

        /*
        $adresse = 'smailallad@gmail.com';
        $name   = 'Smail ALLAD';
        $id     = 'allad.smail';
        $mp     = 'testmp';
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('smailallad@gmail.com')
            ->setTo($adresse)
            ->setBody(
                $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                    '@App/Employe/registration.html.twig',
                    [   'name'  => $name,
                        'mp'    => $mp,
                        'id'    => $id,
                    ]
                ),
                'text/html'
            )

            // you can remove the following code if you don't define a text version for your emails
        ;

        //$mailer = new Swift_Mailer();
        $mailer->send($message);





        /*
        $this->get('session')->getFlashBag()->add('danger', 'Message danger.');
        $this->get('session')->getFlashBag()->add('warning', 'Message warning.');
        $this->get('session')->getFlashBag()->add('info', 'Message info.');
        $this->get('session')->getFlashBag()->add('success', 'Message success.');
        */


/*        $prestation = $this->getDoctrine()->getRepository('AppBundle:Prestation')->find(100);
        $zone = $this->getDoctrine()->getRepository('AppBundle:Zone')->getAddZone($prestation);
       
        $zone = $zone->getQuery()->getResult();
       


        $data = $this->getDoctrine()->getRepository('AppBundle:FamilleDepense')->findAll();
        $i =1;
        foreach ($data as $d){
            $a[]= array (
            "id"    => $d->getId(),
            "nom"   => $d->getNom(),
            "ch1"   => $i,
            );
            $i++;
        }
        
        */

        //throw \Exception("Arret");

        return $this->redirect($this->generateUrl('homepage'));

                /*** */
        
    }



    /**
     * @Route("test_excel",name="test_excel")
     */
    public function test_excelAction()
    {
        
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();

        $objPHPExcel->getProperties()->setCreator("liuggio")
           ->setLastModifiedBy("Giulio De Donato")
           ->setTitle("Office 2005 XLSX Test Document")
           ->setSubject("Office 2005 XLSX Test Document")
           ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
           ->setKeywords("office 2005 openxml php")
           ->setCategory("Test result file");

        //************************** */
        $objPHPExcel->setActiveSheetIndex(0)
           ->setCellValue('A1', 'Héloô')
           ->setCellValue('B2', 'world!');
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // The save method is documented in the official PHPExcel library
        $writer->save('/home/smail/Documents/Temp/filename.xlsx');



        // ask the service for a excel object
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();

        $objPHPExcel->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");
        $dateTimeNow = time(); 
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!')
            ->setCellValue('A4', 'Miscellaneous glyphs')
            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç')
            ->setCellValue('A6', 'Number')
            ->setCellValue('B6', 'Float')
            ->setCellValue('C6', 1255534.56)
            ->setCellValue('A7', 'Number')
            ->setCellValue('B7', 'Negative')
            ->setCellValue('C7', -7.89)
            ->setCellValue('A9', 'Date/Time')
            ->setCellValue('B9', 'Date')
            ->setCellValue('C9', \PHPExcel_Shared_Date::PHPToExcel( $dateTimeNow ));
        $objPHPExcel->setActiveSheetIndex(0)
            ->getStyle('C9')->getNumberFormat()->setFormatCode("dd/mm/yyyy");


        $objRichText = new \PHPExcel_RichText();
        $objRichText->createText('你好 ');
        $objPayable = $objRichText->createTextRun('你 好 吗？');
        $objPayable->getFont()->setBold(true);
        $objPayable->getFont()->setItalic(true);
        $objPayable->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_DARKGREEN ) );

        $objRichText->createText(', unless specified otherwise on the invoice.');

        $objPHPExcel->setActiveSheetIndex(0) ->setCellValue('A13', 'Rich Text')
                                                ->setCellValue('C13', $objRichText);

        $objRichText2 = new \PHPExcel_RichText();
        $objRichText2->createText("black text\n");

        $objRed = $objRichText2->createTextRun("red text");
        $objRed->getFont()->setColor( new \PHPExcel_Style_Color(\PHPExcel_Style_Color::COLOR_RED  ) );

        $objPHPExcel->setActiveSheetIndex(0)->getCell("C14")->setValue($objRichText2);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle("C14")->getAlignment()->setWrapText(true);


        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);

        // Create a first sheet
        //// echo date('H:i:s') , " Add data" , EOL;
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "Firstname");
        $objPHPExcel->getActiveSheet()->setCellValue('B1', "Lastname");
        $objPHPExcel->getActiveSheet()->setCellValue('C1', "Phone");
        $objPHPExcel->getActiveSheet()->setCellValue('D1', "Fax");
        $objPHPExcel->getActiveSheet()->setCellValue('E1', "Is Client ?");


        // Hide "Phone" and "fax" column
        //// echo date('H:i:s') , " Hide 'Phone' and 'fax' columns" , EOL;
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(false);


        // Set outline levels
        //// echo date('H:i:s') , " Set outline levels" , EOL;
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setOutlineLevel(1)
                                                            ->setVisible(false)
                                                            ->setCollapsed(true);

        // Freeze panes
        //// echo date('H:i:s') , " Freeze panes" , EOL;
        $objPHPExcel->getActiveSheet()->freezePane('A2');


        // Rows to repeat at top
        //// echo date('H:i:s') , " Rows to repeat at top" , EOL;
        $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);


        // Add data
        for ($i = 2; $i <= 5000; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "FName $i")
                                        ->setCellValue('B' . $i, "LName $i")
                                        ->setCellValue('C' . $i, "PhoneNo $i")
                                        ->setCellValue('D' . $i, "FaxNo $i")
                                        ->setCellValue('E' . $i, true);
        }



        $objPHPExcel->getActiveSheet()->setTitle('Simple');

        //**************************** */
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(2);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Description')
                                    ->setCellValue('B1', 'Amount');

        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Paycheck received')
                                    ->setCellValue('B2', 100);

        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Cup of coffee bought')
                                    ->setCellValue('B3', -1.5);

        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Cup of coffee bought')
                                    ->setCellValue('B4', -1.5);

        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'Cup of tea bought')
                                    ->setCellValue('B5', -1.2);

        $objPHPExcel->getActiveSheet()->setCellValue('A6', 'Found some money')
                                    ->setCellValue('B6', 8);

        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Total:')
                                    ->setCellValue('B7', '=SUM(B2:B6)');


        // Set column widths
        //echo date('H:i:s') , " Set column widths" , EOL;
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);


        $objConditional1 = new \PHPExcel_Style_Conditional();
        $objConditional1->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)
                        ->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_BETWEEN)
                        ->addCondition('200')
                        ->addCondition('400');
        $objConditional1->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_YELLOW);
        $objConditional1->getStyle()->getFont()->setBold(true);
        $objConditional1->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

        $objConditional2 = new \PHPExcel_Style_Conditional();
        $objConditional2->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)
                        ->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN)
                        ->addCondition('0');
        $objConditional2->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_RED);
        $objConditional2->getStyle()->getFont()->setItalic(true);
        $objConditional2->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

        $objConditional3 = new \PHPExcel_Style_Conditional();
        $objConditional3->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)
                        ->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_GREATERTHANOREQUAL)
                        ->addCondition('0');
        $objConditional3->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_GREEN);
        $objConditional3->getStyle()->getFont()->setItalic(true);
        $objConditional3->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

        $conditionalStyles = $objPHPExcel->getActiveSheet()->getStyle('B2')->getConditionalStyles();
        array_push($conditionalStyles, $objConditional1);
        array_push($conditionalStyles, $objConditional2);
        array_push($conditionalStyles, $objConditional3);
        $objPHPExcel->getActiveSheet()->getStyle('B2')->setConditionalStyles($conditionalStyles);


        //	duplicate the conditional styles across a range of cells
        // echo date('H:i:s') , " Duplicate the conditional formatting across a range of cells" , EOL;
        $objPHPExcel->getActiveSheet()->duplicateConditionalStyle(
                        $objPHPExcel->getActiveSheet()->getStyle('B2')->getConditionalStyles(),
                        'B3:B7'
                    );


        // Set fonts
        // echo date('H:i:s') , " Set fonts" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
        //$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A7:B7')->getFont()->setBold(true);
        //$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);


        // Set header and footer. When no different headers for odd/even are used, odd header is assumed.
        // echo date('H:i:s') , " Set header/footer" , EOL;
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BPersonal cash register&RPrinted on &D');
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');


        // Set page orientation and size
        // echo date('H:i:s') , " Set page orientation and size" , EOL;
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        // Rename worksheet
        // echo date('H:i:s') , " Rename worksheet" , EOL;
        $objPHPExcel->getActiveSheet()->setTitle('Invoice');
        /***************************** */
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(3);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "Firstname")
                                    ->setCellValue('B1', "Lastname")
                                    ->setCellValue('C1', "Phone")
                                    ->setCellValue('D1', "Fax")
                                    ->setCellValue('E1', "Is Client ?");


        // Add data
        for ($i = 2; $i <= 50; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "FName $i");
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, "LName $i");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, "PhoneNo $i");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, "FaxNo $i");
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, true);

            // Add page breaks every 10 rows
            if ($i % 10 == 0) {
                // Add a page break
                $objPHPExcel->getActiveSheet()->setBreak( 'A' . $i, \PHPExcel_Worksheet::BREAK_ROW );
            }
        }

        /**************************** */
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(4);
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Invoice');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', \PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '#12566');

        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Product Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Description');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Price');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Amount');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', 'Total');

        $objPHPExcel->getActiveSheet()->setCellValue('A4', '1001');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'PHP for dummies');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', '20');
        $objPHPExcel->getActiveSheet()->setCellValue('D4', '1');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', '=IF(D4<>"",C4*D4,"")');

        $objPHPExcel->getActiveSheet()->setCellValue('A5', '1012');
        $objPHPExcel->getActiveSheet()->setCellValue('B5', 'OpenXML for dummies');
        $objPHPExcel->getActiveSheet()->setCellValue('C5', '22');
        $objPHPExcel->getActiveSheet()->setCellValue('D5', '2');
        $objPHPExcel->getActiveSheet()->setCellValue('E5', '=IF(D5<>"",C5*D5,"")');

        $objPHPExcel->getActiveSheet()->setCellValue('E6', '=IF(D6<>"",C6*D6,"")');
        $objPHPExcel->getActiveSheet()->setCellValue('E7', '=IF(D7<>"",C7*D7,"")');
        $objPHPExcel->getActiveSheet()->setCellValue('E8', '=IF(D8<>"",C8*D8,"")');
        $objPHPExcel->getActiveSheet()->setCellValue('E9', '=IF(D9<>"",C9*D9,"")');

        $objPHPExcel->getActiveSheet()->setCellValue('D11', 'Total excl.:');
        $objPHPExcel->getActiveSheet()->setCellValue('E11', '=SUM(E4:E9)');

        $objPHPExcel->getActiveSheet()->setCellValue('D12', 'VAT:');
        $objPHPExcel->getActiveSheet()->setCellValue('E12', '=E11*0.21');

        $objPHPExcel->getActiveSheet()->setCellValue('D13', 'Total incl.:');
        $objPHPExcel->getActiveSheet()->setCellValue('E13', '=E11+E12');

        // Add comment
        // echo date('H:i:s') , " Add comments" , EOL;

        $objPHPExcel->getActiveSheet()->getComment('E11')->setAuthor('PHPExcel');
        $objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('PHPExcel:');
        $objCommentRichText->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun("\r\n");
        $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('Total amount on the current invoice, excluding VAT.');

        $objPHPExcel->getActiveSheet()->getComment('E12')->setAuthor('PHPExcel');
        $objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun('PHPExcel:');
        $objCommentRichText->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun("\r\n");
        $objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun('Total amount of VAT on the current invoice.');

        $objPHPExcel->getActiveSheet()->getComment('E13')->setAuthor('PHPExcel');
        $objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun('PHPExcel:');
        $objCommentRichText->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun("\r\n");
        $objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun('Total amount on the current invoice, including VAT.');
        $objPHPExcel->getActiveSheet()->getComment('E13')->setWidth('100pt');
        $objPHPExcel->getActiveSheet()->getComment('E13')->setHeight('100pt');
        $objPHPExcel->getActiveSheet()->getComment('E13')->setMarginLeft('150pt');
        $objPHPExcel->getActiveSheet()->getComment('E13')->getFillColor()->setRGB('EEEEEE');


        // Add rich-text string
        // echo date('H:i:s') , " Add rich-text string" , EOL;
        $objRichText = new \PHPExcel_RichText();
        $objRichText->createText('This invoice is ');

        $objPayable = $objRichText->createTextRun('payable within thirty days after the end of the month');
        $objPayable->getFont()->setBold(true);
        $objPayable->getFont()->setItalic(true);
        $objPayable->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_DARKGREEN ) );

        $objRichText->createText(', unless specified otherwise on the invoice.');

        $objPHPExcel->getActiveSheet()->getCell('A18')->setValue($objRichText);

        // Merge cells
        // echo date('H:i:s') , " Merge cells" , EOL;
        $objPHPExcel->getActiveSheet()->mergeCells('A18:E22');
        $objPHPExcel->getActiveSheet()->mergeCells('A28:B28');		// Just to test...
        $objPHPExcel->getActiveSheet()->unmergeCells('A28:B28');	// Just to test...

        // Protect cells
        // echo date('H:i:s') , " Protect cells" , EOL;
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);	// Needs to be set to true in order to enable any worksheet protection!
        $objPHPExcel->getActiveSheet()->protectCells('A3:E13', 'PHPExcel');

        // Set cell number formats
        // echo date('H:i:s') , " Set cell number formats" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('E4:E13')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

        // Set column widths
        // echo date('H:i:s') , " Set column widths" , EOL;
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);

        // Set fonts
        // echo date('H:i:s') , " Set fonts" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setName('Candara');
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(20);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);

        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);

        $objPHPExcel->getActiveSheet()->getStyle('D13')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E13')->getFont()->setBold(true);

        // Set alignments
        // echo date('H:i:s') , " Set alignments" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('D11')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D12')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D13')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $objPHPExcel->getActiveSheet()->getStyle('A18')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $objPHPExcel->getActiveSheet()->getStyle('A18')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setShrinkToFit(true);

        // Set thin black border outline around column
        // echo date('H:i:s') , " Set thin black border outline around column" , EOL;
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                ),
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('A4:E10')->applyFromArray($styleThinBlackBorderOutline);


        // Set thick brown border outline around "Total"
        // echo date('H:i:s') , " Set thick brown border outline around Total" , EOL;
        $styleThickBrownBorderOutline = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'FF993300'),
                ),
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('D13:E13')->applyFromArray($styleThickBrownBorderOutline);

        // Set fills
        // // echo date('H:i:s') , " Set fills" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('FF808080');

        // Set style for header row using alternative method
        // echo date('H:i:s') , " Set style for header row using alternative method" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('A3:E3')->applyFromArray(
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

        $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray(
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

        $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    )
                )
        );

        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray(
                array(
                    'borders' => array(
                        'right'     => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                )
        );

        // Unprotect a cell
        // echo date('H:i:s') , " Unprotect a cell" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getProtection()->setLocked(\PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

        // Add a hyperlink to the sheet
        // echo date('H:i:s') , " Add a hyperlink to an external website" , EOL;
        $objPHPExcel->getActiveSheet()->setCellValue('E26', 'www.phpexcel.net');
        $objPHPExcel->getActiveSheet()->getCell('E26')->getHyperlink()->setUrl('http://www.phpexcel.net');
        $objPHPExcel->getActiveSheet()->getCell('E26')->getHyperlink()->setTooltip('Navigate to website');
        $objPHPExcel->getActiveSheet()->getStyle('E26')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        // echo date('H:i:s') , " Add a hyperlink to another cell on a different worksheet within the workbook" , EOL;
        $objPHPExcel->getActiveSheet()->setCellValue('E27', 'Terms and conditions');
        $objPHPExcel->getActiveSheet()->getCell('E27')->getHyperlink()->setUrl("sheet://'Terms and conditions'!A1");
        $objPHPExcel->getActiveSheet()->getCell('E27')->getHyperlink()->setTooltip('Review terms and conditions');
        $objPHPExcel->getActiveSheet()->getStyle('E27')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        // Add a drawing to the worksheet
        // echo date('H:i:s') , " Add a drawing to the worksheet" , EOL;
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('./images/officelogo.png');
        $objDrawing->setHeight(36);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // Add a drawing to the worksheet
        // echo date('H:i:s') , " Add a drawing to the worksheet" , EOL;
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Paid');
        $objDrawing->setDescription('Paid');
        $objDrawing->setPath('./images/paid.png');
        $objDrawing->setCoordinates('B15');
        $objDrawing->setOffsetX(110);
        $objDrawing->setRotation(25);
        $objDrawing->getShadow()->setVisible(true);
        $objDrawing->getShadow()->setDirection(45);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // Add a drawing to the worksheet
        // echo date('H:i:s') , " Add a drawing to the worksheet" , EOL;
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('PHPExcel logo');
        $objDrawing->setDescription('PHPExcel logo');
        $objDrawing->setPath('./images/phpexcel_logo.png');
        $objDrawing->setHeight(36);
        $objDrawing->setCoordinates('D24');
        $objDrawing->setOffsetX(10);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // Play around with inserting and removing rows and columns
        // echo date('H:i:s') , " Play around with inserting and removing rows and columns" , EOL;
        $objPHPExcel->getActiveSheet()->insertNewRowBefore(6, 10);
        $objPHPExcel->getActiveSheet()->removeRow(6, 10);
        $objPHPExcel->getActiveSheet()->insertNewColumnBefore('E', 5);
        $objPHPExcel->getActiveSheet()->removeColumn('E', 5);

        // Set header and footer. When no different headers for odd/even are used, odd header is assumed.
        // echo date('H:i:s') , " Set header/footer" , EOL;
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

        // Set page orientation and size
        // echo date('H:i:s') , " Set page orientation and size" , EOL;
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename first worksheet
        // echo date('H:i:s') , " Rename first worksheet" , EOL;
        $objPHPExcel->getActiveSheet()->setTitle('Invoice');

        /****************************** */
        /****************************** */
        // Create a new worksheet, after the default sheet
        // echo date('H:i:s') , " Create a second Worksheet object" , EOL;
        $objPHPExcel->createSheet();
        // Add some data to the second sheet, resembling some different data types
        // echo date('H:i:s') , " Add some data" , EOL;
        $objPHPExcel->setActiveSheetIndex(5);
        // Llorem ipsum...
        $sLloremIpsum = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus eget ante. Sed cursus nunc semper tortor. Aliquam luctus purus non elit. Fusce vel elit commodo sapien dignissim dignissim. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur accumsan magna sed massa. Nullam bibendum quam ac ipsum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin augue. Praesent malesuada justo sed orci. Pellentesque lacus ligula, sodales quis, ultricies a, ultricies vitae, elit. Sed luctus consectetuer dolor. Vivamus vel sem ut nisi sodales accumsan. Nunc et felis. Suspendisse semper viverra odio. Morbi at odio. Integer a orci a purus venenatis molestie. Nam mattis. Praesent rhoncus, nisi vel mattis auctor, neque nisi faucibus sem, non dapibus elit pede ac nisl. Cras turpis.';

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Terms and conditions');
        $objPHPExcel->getActiveSheet()->setCellValue('A3', $sLloremIpsum);
        $objPHPExcel->getActiveSheet()->setCellValue('A4', $sLloremIpsum);
        $objPHPExcel->getActiveSheet()->setCellValue('A5', $sLloremIpsum);
        $objPHPExcel->getActiveSheet()->setCellValue('A6', $sLloremIpsum);

        // Set the worksheet tab color
        // echo date('H:i:s') , " Set the worksheet tab color" , EOL;
        $objPHPExcel->getActiveSheet()->getTabColor()->setARGB('FF0094FF');;

        // Set alignments
        // echo date('H:i:s') , " Set alignments" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('A3:A6')->getAlignment()->setWrapText(true);

        // Set column widths
        // echo date('H:i:s') , " Set column widths" , EOL;
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);

        // Set fonts
        // echo date('H:i:s') , " Set fonts" , EOL;
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Candara');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $objPHPExcel->getActiveSheet()->getStyle('A3:A6')->getFont()->setSize(8);

        // Add a drawing to the worksheet
        // echo date('H:i:s') , " Add a drawing to the worksheet" , EOL;
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Terms and conditions');
        $objDrawing->setDescription('Terms and conditions');
        $objDrawing->setPath('./images/termsconditions.png');
        $objDrawing->setCoordinates('B14');
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        // Set page orientation and size
        // echo date('H:i:s') , " Set page orientation and size" , EOL;
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename second worksheet
        // echo date('H:i:s') , " Rename second worksheet" , EOL;
        $objPHPExcel->getActiveSheet()->setTitle('Terms and conditions');

        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(6);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Year')
                              ->setCellValue('B1', 'Quarter')
                              ->setCellValue('C1', 'Country')
                              ->setCellValue('D1', 'Sales');

        $dataArray = array(array('2010',	'Q1',	'United States',	790),
                        array('2010',	'Q2',	'United States',	730),
                        array('2010',	'Q3',	'United States',	860),
                        array('2010',	'Q4',	'United States',	850),
                        array('2011',	'Q1',	'United States',	800),
                        array('2011',	'Q2',	'United States',	700),
                        array('2011',	'Q3',	'United States',	900),
                        array('2011',	'Q4',	'United States',	950),
                        array('2010',	'Q1',	'Belgium',			380),
                        array('2010',	'Q2',	'Belgium',			390),
                        array('2010',	'Q3',	'Belgium',			420),
                        array('2010',	'Q4',	'Belgium',			460),
                        array('2011',	'Q1',	'Belgium',			400),
                        array('2011',	'Q2',	'Belgium',			350),
                        array('2011',	'Q3',	'Belgium',			450),
                        array('2011',	'Q4',	'Belgium',			500),
                        array('2010',	'Q1',	'UK',				690),
                        array('2010',	'Q2',	'UK',				610),
                        array('2010',	'Q3',	'UK',				620),
                        array('2010',	'Q4',	'UK',				600),
                        array('2011',	'Q1',	'UK',				720),
                        array('2011',	'Q2',	'UK',				650),
                        array('2011',	'Q3',	'UK',				580),
                        array('2011',	'Q4',	'UK',				510),
                        array('2010',	'Q1',	'France',			510),
                        array('2010',	'Q2',	'France',			490),
                        array('2010',	'Q3',	'France',			460),
                        array('2010',	'Q4',	'France', 			590),
                        array('2011',	'Q1',	'France',			620),
                        array('2011',	'Q2',	'France',			650),
                        array('2011',	'Q3',	'France',			415),
                        array('2011',	'Q4',	'France', 			570),
                        array('2010',	'Q1',	'Germany',			720),
                        array('2010',	'Q2',	'Germany',			680),
                        array('2010',	'Q3',	'Germany',			640),
                        array('2010',	'Q4',	'Germany',			660),
                        array('2011',	'Q1',	'Germany',			680),
                        array('2011',	'Q2',	'Germany',			620),
                        array('2011',	'Q3',	'Germany',			710),
                        array('2011',	'Q4',	'Germany',			690),
                        array('2010',	'Q1',	'Spain',			510),
                        array('2010',	'Q2',	'Spain',			490),
                        array('2010',	'Q3',	'Spain',			470),
                        array('2010',	'Q4',	'Spain',			420),
                        array('2011',	'Q1',	'Spain',			460),
                        array('2011',	'Q2',	'Spain',			390),
                        array('2011',	'Q3',	'Spain',			430),
                        array('2011',	'Q4',	'Spain',			415),
                        array('2010',	'Q1',	'Italy',			440),
                        array('2010',	'Q2',	'Italy',			410),
                        array('2010',	'Q3',	'Italy',			420),
                        array('2010',	'Q4',	'Italy',			450),
                        array('2011',	'Q1',	'Italy',			430),
                        array('2011',	'Q2',	'Italy',			370),
                        array('2011',	'Q3',	'Italy',			350),
                        array('2011',	'Q4',	'Italy',			335),
                        );
        $objPHPExcel->getActiveSheet()->fromArray($dataArray, NULL, 'A2');

        // Set title row bold
        //echo date('H:i:s').' Set title row bold'.EOL;
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

        // Set autofilter
        //echo date('H:i:s').' Set autofilter'.EOL;
        // Always include the complete filter range!
        // Excel does support setting only the caption
        // row, but that's not a best practise...
        //$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
        $objPHPExcel->getActiveSheet()->setAutoFilter("A1:B1");

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet














        //**************************** */

                        // ENREGISTER LE FICHIER
        
        //**************************** */
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        // $objPHPExcel->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'PhpExcelFileSample.xlsx'
        );


        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response; 

    }
    /**
     * @Route("db",name="db")
     */
    public function bdAction()
    {   //exit;
    
    }
}
