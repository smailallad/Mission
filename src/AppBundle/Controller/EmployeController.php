<?php
namespace AppBundle\Controller;
//use Symfony\Component\Security\Core\Security;
use DateTime;
use AppBundle\Entity\User;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\EmployeType;
use AppBundle\Entity\Recrutement;
use AppBundle\Entity\FonctionUser;
use AppBundle\Entity\Licenciement;
use AppBundle\Form\EmployeEditType;
use AppBundle\Form\RecrutementType;
use AppBundle\Form\FonctionUserType;
use AppBundle\Form\LicenciementType;
use AppBundle\Form\EmployeFilterType;
use AppBundle\Form\RecrutementEditType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/employe")
 * @Security("has_role('ROLE_ADMINISTRATION')")
 */
class EmployeController extends Controller
{
    /**
     * @Route("/new",name="employe_new")
     */
    public function newAction(Request $request,\Swift_Mailer $mailer)
    {
        $passwordEncoder = $this->get('security.password_encoder');
        $user = new User();
        $manager = $this->getDoctrine()->getManager();
        $groupe = $this->getDoctrine()->getRepository('AppBundle:Groupes')->findOneBy(['groupname' =>'USER']);
        dump($groupe);
        $user->setGroupes($groupe);
        dump($user);

        $form = $this->createForm(EmployeType::class, $user);
        $form->handleRequest($request); 
        if ($form->isSubmitted())
        {   //throw new \Exception('Message1');
        
            //throw new \Exception('Message2');
            $recruter = $form['recruter']->getData();
            $fonction = $form['fonction']->getData();
            $recrutement = new Recrutement();
            $recrutement->setRecruter($recruter);
            $recrutement->setUser($user);
            $fonctionUser = new FonctionUser();
            $fonctionUser->setFonction($fonction);
            $fonctionUser->setDatefonction($recruter);
            $fonctionUser->setUser($user);
            $date_courante = new DateTime(date("Y-m-d"));
            $interval = ($form['naissance']->getData()->diff($form['recruter']->getData()));
            $r=$interval->format('%R');
            $age=$interval->format('%y');
            if ($r=="-")
            {
                $age=$age*(-1);
            }
            if ($recruter > $date_courante)
            {
                $this->get('session')->getFlashBag()->add('danger', "Date de recrutement doit être inférieur ou égal à la date d'aujourd'hui");
            } elseif ($age < 15 )
            {
                $this->get('session')->getFlashBag()->add('danger', "L'employé doit avoir plus de 15 ans, veuillez vérifier la date de naissance et la date de recrutement.");
            }else
            {                
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                
                if ($form->isValid())
                {
                    $manager->persist($user);
                    $manager->persist($recrutement);
                    //dump($fonctionUser);
                    $manager->persist($fonctionUser);
                    $manager->flush();
                    /*** */
                    $adresse = $user->getEmail();
                    $name = $user->getNom();
                    $id   = $user->getUsername();
                    /*
                    $connection = new Swift_Connection_SMTP('smtp.gmail.com', 465, Swift_Connection_SMTP::ENC_SSL);
                    $connection->setUsername('romain@gmail.com');
                    $connection->setPassword('SuperSecurePassword');
                    
                    $mailer = new Swift($connection);
                    */
                    $message = (new \Swift_Message('Hello Email'))
                        ->setFrom(' smailallad@gmail.com')
                        ->setTo($adresse )
                        ->addBCc('smailallad@gmail.com')
                        ->setBody(
                            $this->renderView(
                                // app/Resources/views/Emails/registration.html.twig
                                '@App/Employe/registration.html.twig',
                                [   'id'    => $id,
                                    'name'  => $name,
                                    'mp'    => $password]
                            ),
                            'text/html'
                        )

                        // you can remove the following code if you don't define a text version for your emails
                    ;

                    //$mailer = new Swift_Mailer();
                    $mailer->send($message);

                    /*** */

                    //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                    $cryptage = $this->container->get('my.cryptage');
                    return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($user->getId()))));
                }
            }
        
        }
        
        return $this->render('@App/Employe/new.html.twig', array(
            'employe' => $user,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/employe",name="employe")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EmployeFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'employe', 'employe'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:User')->createQueryBuilder('u');
        $paginator = $this->filter($form, $qb, 'employe');
        $forme=$form->createView();
        //dump($paginator);
        return $this->render('@App/Employe/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/{id}/edit",name="employe_edit").
     * id employe
     */
    public function editAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $employe = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $editForm = $this->createForm(EmployeEditType::class, $employe, array(
            'action' => $this->generateUrl('employe_edit', array('id' => $cryptage->my_encrypt($employe->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            //$this->get('session')->getFlashBag()->add('danger', 'Erreur! Veuillez verifier vos données...');
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('employe_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Employe/edit.html.twig', array(
            'employe' => $employe,
            'edit_form'   => $editForm->createView(),
        ));
    }
        /**
     * @Route("/{id}/show",name="employe_show")
     */
    public function showAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $recrutements = $this->getDoctrine()->getRepository('AppBundle:Recrutement')->listeRecrutement($id);
        $employe = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        //dump($id);dump($employe);
        $fonctions = $this->getDoctrine()->getRepository('AppBundle:FonctionUser')->findByUser($employe);
        $deleteForm = $this->createDeleteForm($id, 'employe_delete');
        //dump($fonctions);
        return $this->render('@App/Employe/show.html.twig', array(
            'employe'       => $employe,
            'recrutements'  => $recrutements,
            'fonctions'     => $fonctions,
            'delete_form'   => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/delete",name="employe_delete")
     *
     */
    public function deleteAction($id, Request $request)
    {
        $cryptage       = $this->container->get('my.cryptage');
        $manager        = $this->getDoctrine()->getManager();
        $employe        = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $mission        = $this->getDoctrine()->getRepository('AppBundle:Mission')->findOneByUser($id);
        $intervention   = $this->getDoctrine()->getRepository('AppBundle:InterventionUser')->findOneByUser($id);
        $fraisMission   = $this->getDoctrine()->getRepository('AppBundle:FraisMission')->findOneByUser($id);
        
        if (($mission === null) and ($intervention === null) and ($fraisMission === null)){
            $fonctions = $this->getDoctrine()->getRepository('AppBundle:FonctionUser')->findByUser($id);
            foreach($fonctions as $fonction) {
                $manager->remove($fonction);  
            }
            $recrutements = $this->getDoctrine()->getRepository('AppBundle:Recrutement')->findByUser($id);
            foreach($recrutements as $recrutement) {
                $manager->remove($recrutement);
            }
            $manager->remove($employe);
            $manager->flush();
            return $this->redirect($this->generateUrl('employe'));
        }else{
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet employé, verifier les missions, les interventions ou les frais de mission.');
            return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($id))));
        }
        
    }

    /**
     * @Route("/{id}/recrutement_new",name="recrutement_new")
     * id user
     */
    public function recrutementNewAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $recrutement = new Recrutement();
        $recrutement->setUser($user);
        $form = $this->createForm(RecrutementType::class, $recrutement, array(
            'action' => $this->generateUrl('recrutement_new', array('id' => $cryptage->my_encrypt($id))),
            'method' => 'PUT',
        ));
        if ($form->handleRequest($request)->isValid())
        {
            $date_courante = new DateTime(date("Y-m-d"));
            $erreur = false;
            if ($form['recruter']->getData() > $date_courante)
            {
                $this->get('session')->getFlashBag()->add('danger', "Date de recrutement doit être inférieur ou égal à la date d'aujourd'hui");
                $erreur = true;
            }
            $licenciementDernier = $this->getDoctrine()->getRepository("AppBundle:Licenciement")->getLicenciementDernier($id);
            if ($licenciementDernier != false)
            {
                if ($licenciementDernier->getLicencier() >= $form['recruter']->getData())
                {
                    $this->get('session')->getFlashBag()->add('danger', "Date de recrutement doit être supérieur à la date dernier licenciement: ".$licenciementDernier->getLicencier()->format("d/m/Y"));
                    $erreur = true;
                }
            }
            if (!$erreur)
            {   
                $recruter = $form['recruter']->getData();
                $fonctionUser = new FonctionUser();
                $fonctionUser->setFonction($form['fonction']->getData());
                $fonctionUser->setDatefonction($recruter);
                //$date = new DateTime('2000-01-01');
                //$fonctionUser->setDatefonction($date);
                $fonctionUser->setUser($user);
                //dump($recrutement);
                $this->getDoctrine()->getManager()->persist($recrutement);
                //dump($fonctionUser);
                $this->getDoctrine()->getManager()->persist($fonctionUser);
                $this->getDoctrine()->getManager()->flush();
                //$this->get('session')->getFlashBag()->add('success', "Enregistrement avec succès.");
                return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($id))));
            }
        }
        return $this->render('@App/Employe/recrutement.html.twig', array(
            'recrutement'   => $recrutement,
            'user'          => $user->getId(),
            'form'          => $form->createView()
        ));
    }
    /**
     * @Route("/{id}/recrutement_edit",name="recrutement_edit")
     * id recrutement
     */
    public function recrutementEditAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $recrutement = $this->getDoctrine()->getRepository('AppBundle:Recrutement')->find($id);
                $form = $this->createForm(RecrutementEditType::class, $recrutement, array(
            'action'    => $this->generateUrl('recrutement_edit', array('id' => $cryptage->my_encrypt($id))),
            'method'    => 'PUT',
        ));
        if ($form->handleRequest($request)->isValid())
        {
            $licenciement = $this->getDoctrine()->getRepository('AppBundle:Licenciement')->findByRecrutement($id);
            $user = $recrutement->getUser()->getId();
            $dateRecrutement = $recrutement->getRecruter();
            $precedent = $this->getDoctrine()->getRepository('AppBundle:Licenciement')->getLicenciementPrecedent($user,$dateRecrutement);
            $msg="";
            if ($precedent != false)
            {
                $msg = 'Date de recrutement doit être supérieur à : ' . $precedent->getLicencier()->format("d/m/Y");
            }
            if (count($licenciement)>0)
            {
                $suivant = $licenciement[0];
                $msg = $msg.' est inférieur à : '.$suivant->getLicencier()->format("d/m/Y");
            }else
            {
                $suivant = false;
            }
            $erreur = false;
            if ($precedent != false)
            {
                if ($form['recruter']->getData() <= $precedent->getLicencier())
                {
                    $erreur = true;
                }
            }
            if ($suivant != false)
            {
                if ($form['recruter']->getData() >= $suivant->getLicencier())
                {
                    $erreur = true;
                }
            }
            if ($erreur)
            {
                $this->get('session')->getFlashBag()->add('danger', $msg);
            }else
            {
                $this->getDoctrine()->getManager()->flush();
                //$this->get('session')->getFlashBag()->add('success', "Enregistrement avec succès.");
                return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($user))));
            }
        }
        return $this->render('@App/Employe/recrutement.html.twig', array(
            'user'          =>$recrutement->getUser()->getId(),
            'recrutement'   => $recrutement,
            'form'          => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/recrutement_delete",name="recrutement_delete",options = { "expose" = true },)
     *
     */
    public function recrutementDeleteAction(Recrutement $recrutement)
    {
        $cryptage = $this->container->get('my.cryptage');
        $user = $recrutement->getUser()->getId();
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($recrutement);
        $manager->flush();
        //$this->get('session')->getFlashBag()->add('success', 'Suppréssion effectuer avec sucées.');
        return $this->redirect($this->generateUrl('employe_show',array('id' => $cryptage->my_encrypt($user))));
    }
    /**
     * @Route("/{id}/licenciement_new",name="licenciement_new")
     * id recrutement
     */
    public function licenciementNewAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $recrutement = $this->getDoctrine()->getRepository('AppBundle:Recrutement')->find($id);
        $user = $recrutement->getUser();
        $recruter = $recrutement->getRecruter();
        $licenciement = new Licenciement();
        $licenciement->setRecrutement($recrutement);
        $form = $this->createForm(LicenciementType::class, $licenciement, array(
            'action' => $this->generateUrl('licenciement_new', array('id' => $cryptage->my_encrypt($id))),
            'method' => 'PUT',
        ));
        if ($form->handleRequest($request)->isValid())
        {
            $date_courante = new DateTime(date("Y-m-d"));
            $erreur = false;
            if ($form['licencier']->getData() > $date_courante)
            {
                $this->get('session')->getFlashBag()->add('danger', "Date de licenciement doit être inférieur ou égal à la date d'aujourd'hui");
                $erreur = true;
            } elseif ($form['licencier']->getData() <= $recruter)
            {
                $this->get('session')->getFlashBag()->add('danger', "Date de licenciement doit être supérieur ou égal à la date recrutement: ".$recruter->format("d/m/Y"));
                $erreur = true;
            }
            if (!$erreur)
            {
                $user->setActive(false);
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->persist($licenciement);
                $this->getDoctrine()->getManager()->flush();
                //$this->get('session')->getFlashBag()->add('success', "Enregistrement avec succès.");
                return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($user->getId()))));
            }
        }
        return $this->render('@App/Employe/licenciement.html.twig', array(
            'user'            => $user->getId(),
            'licenciement'  => $licenciement,
            'form'          => $form->createView()
        ));
    }
    /**
     * @Route("/{id}/licenciement_edit",name="licenciement_edit")
     * id licenciement
     */
    public function licenciementEditAction($id, Request $request)
   {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $licenciement = $this->getDoctrine()->getRepository('AppBundle:Licenciement')->getLicenciement($id);
        $user = $licenciement->getRecrutement()->getUser();
        $form = $this->createForm(LicenciementType::class, $licenciement, array(
            'action' => $this->generateUrl('licenciement_edit', array('id' => $cryptage->my_encrypt($licenciement->getId()))),
            'method' => 'PUT',
        ));
        if ($form->handleRequest($request)->isValid())
        {
            $recrutement = $licenciement->getRecrutement();
            $manager = $this->getDoctrine()->getManager();
            $suivant = $manager->getRepository('AppBundle:Recrutement')->getRecrutementSuivants($user,$recrutement->getRecruter());
            $msg1 = 'Date de licenciement doit être supérieur à : ' . $recrutement->getRecruter()->format("d/m/Y");
            $msg2 = null;
            $er=false;
            if ($suivant != false)
            {
                $msg2 = 'Date de licenciement doit être inférieur à : ' . $suivant->getRecruter()->format("d/m/Y");
                if ($form['licencier']->getData() >= $suivant->getRecruter())
                {
                    $er =true;
                }
            }
            if ($recrutement->getRecruter() >= $form['licencier']->getData())
            {
                $msg2 = 'Date de licenciement doit être inférieur à : ' . $suivant->getRecruter()->format("d/m/Y");
                $er = true;
            }
            if ($er)
            {
                $this->get('session')->getFlashBag()->add('danger', $msg1);
                if ($msg2 !== null)
                {
                    $this->get('session')->getFlashBag()->add('danger', $msg2);
                }
            }else
            {
                $this->getDoctrine()->getManager()->flush();
                //$this->get('session')->getFlashBag()->add('danger', 'Erreur! Veuillez verifier vos données...');
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($user->getId()))));
            }
        }
        //return $this->redirect($this->generateUrl('licenciement_edit', array('id' => $cryptage->my_encrypt($id))));
        return $this->render('@App/Employe/licenciement.html.twig', array(
                'user'          =>$user->getId(),
                'licenciement'  => $licenciement,
                'form'          => $form->createView()
        ));
    }
    /**
     * @Route("/{id}/licenciement_delete",name="licenciement_delete",options = { "expose" = true })
     * id licenciement
     */
    public function licenciementDeleteAction(Licenciement $licenciement)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $recrutement = $licenciement->getRecrutement();
        $user = $recrutement->getUser()->getId();
        $manager->remove($licenciement);
        $manager->flush();
        //$this->get('session')->getFlashBag()->add('success', 'Suppréssion effectuer avec sucées.');
        return $this->redirect($this->generateUrl('employe_show',array('id' => $cryptage->my_encrypt($user))));
    }
    /**
     * @Route("/{userId}/fonction_employe_new",name="fonction_employe_new")
     *
     */
    public function fonctionEmployeNewAction($userId, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage'); 
        $manager = $this->getDoctrine()->getManager();
        $userId = $cryptage->my_decrypt($userId);
        $user = $manager->getRepository("AppBundle:User")->find($userId);
        $fonctionEmploye = new FonctionUser();
        $fonctionEmploye->setUser($user);
        $form = $this->createForm(FonctionUserType::class, $fonctionEmploye, array(
            'action' => $this->generateUrl('fonction_employe_new', array('userId' => $cryptage->my_encrypt($userId))),
            'method' => 'PUT',
        ));
        if ($form->handleRequest($request)->isValid())
        {   //dump($fonctionEmploye->getDatefonction());dump(($form['datefonction']->getData()));exit;
            $erreur = false;
            $recrutement = $manager->getRepository("AppBundle:Recrutement")->findOneBy(["user" => $userId],["recruter" => "DESC"]);
            //dump($recrutement);
            if ($recrutement->getRecruter() >= $form['datefonction']->getData())
            {
                $this->get('session')->getFlashBag()->add('danger', "Date de la nouvelle fonction doit être supérieur à la dernière date de recrutement:".$recrutement->getRecruter()->format('d/m/Y'));
                $erreur = true;
            }
            if (!$erreur)
            {
                //dump($fonctionEmploye);
                $this->getDoctrine()->getManager()->persist($fonctionEmploye);
                $this->getDoctrine()->getManager()->flush();
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($userId))));
            }
        }
        return $this->render('@App/Employe/fonction.employe.html.twig', array(
            'user'              => $userId,
            'fonctionEmploye'   => $fonctionEmploye,
            'form'              => $form->createView())
        );
    }
    /**
     * @Route("/{userId}/{fonctionId}/fonction_employe_edit",name="fonction_employe_edit")
     *
     */ 
    public function fonctionEmployeEditAction($userId,$fonctionId, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $userId = $cryptage->my_decrypt($userId);
        $fonctionId = $cryptage->my_decrypt($fonctionId);
        $fonctionEmploye = $this->getDoctrine()->getRepository('AppBundle:FonctionUser')->findBy(['user' => $userId,'fonction' =>$fonctionId]);
        if (count($fonctionEmploye)>0)
        {
            $fonctionEmploye = $fonctionEmploye[0];
        }
        //dump($fonctionEmploye);
        //dump(Type::getTypesMap());
        $form = $this->createForm(FonctionUserType::class);
        $form = $this->createForm(FonctionUserType::class, $fonctionEmploye, array(
            'action' => $this->generateUrl('fonction_employe_edit', array('userId' => $cryptage->my_encrypt($userId),'fonctionId' => $cryptage->my_encrypt($fonctionId))),
            'method' => 'PUT',
        ));
        /*
        //dump($request->getMethod());dump($form);
        if ($request->getMethod() == 'PUT')
        {
            $fonctionEmploye->setDatefonction($form['datef']->getData());
        }else{
            $form['datef']->setData($fonctionEmploye->getDatefonction());
        }*/
        $oldDatefonction = $fonctionEmploye->getDatefonction();
        if ($form->handleRequest($request)->isValid())
        {   //dump($fonctionEmploye->getDatefonction());dump(($form['datefonction']->getData()));exit;
            $erreur = false;
            if ($oldDatefonction != $form['datefonction']->getData())
            {
                $manager = $this->getDoctrine()->getManager();
                $recrutement = $manager->getRepository('AppBundle:Recrutement')->findBy(['user' => $userId,'recruter' => $oldDatefonction]);
                if (count($recrutement)>0)
                {
                    $this->get('session')->getFlashBag()->add('danger', "Date de fonction non autoriser pour la modifier, c'est une date de recrutement");
                    $erreur = true;
                }else
                {
                    //$recrutement = $manager->getRepository(Recrutement::class)->getRecrutementFonction($userId,$oldDatefonction);
                    //dump($recrutement);exit;
                }
            }
            if (!$erreur)
            {
                $this->getDoctrine()->getManager()->persist($fonctionEmploye);
                $this->getDoctrine()->getManager()->flush();
                //$this->get('session')->getFlashBag()->add('danger', 'Erreur! Veuillez verifier vos données...');
                //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                return $this->redirect($this->generateUrl('employe_show', array('id' => $cryptage->my_encrypt($userId))));
            }
        }
        return $this->render('@App/Employe/fonction.employe.html.twig', array(
            'user'              => $userId,
            'fonctionEmploye'   => $fonctionEmploye,
            'form'              => $form->createView()));
    }
    /**
     * @Route("/{user}/{fonction}/fonction/employe/delete",name="fonction_employe_delete",options = { "expose" = true })
     */ 
    public function fonctiontDeleteAction($user,$fonction)
    {
        $cryptage = $this->container->get('my.cryptage');
        $manager = $this->getDoctrine()->getManager();
        $fonctionEmploye = $this->getDoctrine()->getRepository('AppBundle:FonctionUser')->findOneBy(['user' => $user,'fonction' =>$fonction]);
        $recruter = $this->getDoctrine()->getRepository('AppBundle:Recrutement')->findOneBy(['user' => $user,'recruter' =>$fonctionEmploye->getDatefonction()]);
        if ($recruter === null ){

            $manager->remove($fonctionEmploye);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Suppréssion effectuer avec sucées.');
        }else{
            $this->get('session')->getFlashBag()->add('danger', "C'est la fonction de recrutement, Suppréssion annulée.");
        }
        //throw new \Exception("Arret");
        
        
        
        return $this->redirect($this->generateUrl('employe_show',array('id' => $cryptage->my_encrypt($user))));
    }

//*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="employe_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('employe', $field, $type);
        return $this->redirect($this->generateUrl('employe'));
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
