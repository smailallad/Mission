<?php
namespace AppBundle\Controller;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\UserMPType;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\UserFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
* @Route("/users")
* @Security("has_role('ROLE_ADMIN')")
*/
class UserController extends Controller
{
     /**
     * @Route("/users", name="admin_users")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserFilterType::Class);
        if (!is_null($response = $this->saveFilter($form, 'user', 'admin_users'))) {
            return $response;echo("UserController ligne: 35"); exit;
        }
        $qb = $manager->getRepository('AppBundle:User')->createQueryBuilder('u');
        $paginator = $this->filter($form, $qb, 'user');
        //$forme=$form->createView();
        return $this->render('@App/User/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }

    /**
     * @Route("/users/email", name="admin_correction_email")
     */
    public function correctionEmailAction()
    {
        $manager = $this->getDoctrine()->getManager();
        
        $users = $manager->getRepository('AppBundle:User')->findAll();
        foreach ($users as $user) {
            $nom = $user->getNom();
            $nom = explode(" ",$nom);
            $newEmail = "";
            for ($i=(count($nom)-1); $i >=0; $i--) { 
                if ($newEmail == "" )
                {
                    $newEmail = strtolower($nom[$i]);
                }else{
                    $newEmail = $newEmail . "." . strtolower($nom[$i]);
                }
            }
            $newEmail = $newEmail . "@rtie-dz.com";
            $user ->setEmail($newEmail);
            $manager->persist($user);
            $manager->flush();

        }
        
        
        return $this->redirect($this->generateUrl('admin_users'));
    }

    /**
    * @Route("/{id}/show",name="admin_users_show")
    */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $deleteForm = $this->createDeleteForm($user->getId(), 'admin_users_delete');
        return $this->render('@App/User/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView()));
    }
    /**
     *@Route("/new",name="admin_users_new")
     *
     */
    public function newAction(Request $request)
    {
        $passwordEncoder = $this->get('security.password_encoder');
        $user = new User();
        $form = $this->createForm(UserType::Class, $user);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $manager->persist($user);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
        }
        return $this->render('@App/User/new.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="admin_users_edit")
     *
     */
    public function editAction($id, Request $request)
    {   
        $cryptage = $this->container->get('my.cryptage');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($cryptage->my_decrypt($id));
        $editForm = $this->createForm(UserType::Class, $user, array(
            'action' => $this->generateUrl('admin_users_edit', array('id' => $id)),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            //$this->get('session')->getFlashBag()->add('danger', 'Erreur! Veuillez verifier vos données...');
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('admin_users_edit', array('id' => $id)));
        }
        return $this->render('@App/User/edit.html.twig', array(
            'user' => $user,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete",name="admin_users_delete")
     *
     */
    public function deleteAction(User $user, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $user->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $id)));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('admin_users'));

        /** */
        /*$form = $this->createDeleteForm($user->getId(), 'admin_users_delete');
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($user);
            $manager->flush();
        }
        return $this->redirect($this->generateUrl('admin_users'));*/
    }
    
    /**
     * @Route("/{id}/mp",name="admin_users_updateMP").
     *
     */
    public function updateMPAction($id,Request $request,\Swift_Mailer $mailer)
    {
        $cryptage = $this->container->get('my.cryptage');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($cryptage->my_decrypt($id));
        $passwordEncoder = $this->get('security.password_encoder');
        $form = $this->createForm(UserMPType::Class, $user, array(
            'action' => $this->generateUrl('admin_users_updateMP', array('id' => $id)),
            'method' => 'POST',
        ));

        $form->handleRequest($request); 
        if ($form->isSubmitted()){  
            if ($form->isValid()){
                $mp= $user->getPassword();
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                
                $manager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Mot de passe changer avec succès.');
                $adresse    = $user->getEmail();
                $name       = $user->getNom();
                $id         = $user->getUsername();
                $mp         = $mp;
                $mail       = $this->container->getParameter('mailer_user');
                $message = (new \Swift_Message('Bonjour'))
                    ->setFrom($mail)
                    ->setTo($adresse)
                    ->addBCc($mail)
                    ->setBody(
                        $this->renderView(
                            // app/Resources/views/Emails/newPassword.html.twig
                            '@App/Employe/newPassword.html.twig',
                            [   'name'  => $name,
                                'mp'    => $mp,
                                'id'    => $id,
                            ]
                        ),
                        'text/html'
                    )

                    // you can remove the following code if you don't define a text version for your emails
                ;
                // .F#-1@2ie]9o
                //$mailer = new Swift_Mailer();
                $mailer->send($message);
                return $this->redirectToRoute('admin_users');
            }
        }
        return $this->render('@App/User/mp.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    //================================================================================================
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
    * @route("/{field}/{type}/sort",name="admin_users_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('user', $field, $type);
        return $this->redirect($this->generateUrl('admin_users'));
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
    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }
    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }
    /**
     * Save filters
     *
     * @param  FormInterface $form
     * @param  string        $name   route/entity name
     * @param  string        $route  route name, if different from entity name
     * @param  array         $params possible route parameters
     * @param  Request       $request
     * @return Response
     */
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
    /**
     * Filter form
     *
     * @param  FormInterface                                       $form
     * @param  QueryBuilder                                        $qb
     * @param  string                                              $name
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
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
    /**
     * Get filters from session
     *
     * @param  string $name
     * @return array
     */
    protected function getFilter($name)
    {   $request = $this->container->get('request_stack')->getCurrentRequest();
        return $request->getSession()->get('filter.' . $name);
    }
    
}
