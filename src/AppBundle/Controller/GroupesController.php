<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Groupes;
use AppBundle\Entity\Roles;

use AppBundle\Form\GroupesType;
use AppBundle\Form\ListeRoleType;


/**
 * @Route("/Groupes")
 * @Security("has_role('ROLE_ADMIN')")
 */
class GroupesController extends Controller
{
    /**
    * @Route("/groupes",name="admin_groups")
    */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $entities = $manager->getRepository(Groupes::Class)->findAll();
        return $this->render('@App/Groupes/index.html.twig', array(
            'entities'  => $entities,
        ));
    }

    /**
    * @Route("/{id}/show",name="admin_groups_show")
    */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);

        $groupes = $this->getDoctrine()->getRepository('AppBundle:Groupes')->find($id);
        $role = new Roles();
        $formRole = $this->createForm(ListeRoleType::class, $role, array(
            'action' => $this->generateUrl('admin_groups_add_roles', array('id' => $groupes->getId())),
            'method' => 'POST',
        ));
        $deleteForm = $this->createDeleteForm($groupes->getId(), 'admin_groups_delete');
        return $this->render('@App/Groupes/show.html.twig', array(
            'groupes' => $groupes,
            'delete_form' => $deleteForm->createView(),
            'formRole' => $formRole->createView(),
            ));
    }

    /**
    * @Route("/new",name="admin_groups_new")
    */

    public function newAction()
    {
        $groupes = new Groupes();
        $form = $this->createForm(GroupsType::Class, $groupes);

        return $this->render('@App/Groupes/new.html.twig', array(
            'groupes' => $groupes,
            'form'   => $form->createView(),
        ));
    }

    /**
    * @Route("/create",name="admin_groups_create")
    */
    public function createAction(Request $request)
    {
        $groupes = new Groupes();
        $form = $this->createForm(GroupsType::Class, $groupes);
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($groupes);
            $manager->flush();

            return $this->redirect($this->generateUrl('admin_groups_show', array('id' => $groupes->getId())));
        }

        return $this->render('@App/Groupes/new.html.twig', array(
            'groupes' => $groupes,
            'form'   => $form->createView(),
        ));
    }

    /**
    * @Route("/{id}/edit",name="admin_groups_edit")
    */

    public function editAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);

        $groupes = $this->getDoctrine()->getRepository('AppBundle:Groupes')->find($id);

        $editForm = $this->createForm(GroupesType::Class, $groupes, array(
            'action' => $this->generateUrl('admin_groups_update', array('id' => $groupes->getId())),
            'method' => 'PUT',
        ));

        $deleteForm = $this->createDeleteForm($groupes->getId(), 'admin_groups_delete');

        return $this->render('@App/Groupes/edit.html.twig', array(
            'groupes' => $groupes,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * @Route("/{id}/update",name="admin_groups_update")
    */

    public function updateAction(Groupes $groupes, Request $request)
    {
        $editForm = $this->createForm(GroupesType::Class, $groupes, array( 
            'action' => $this->generateUrl('admin_groups_update', array('id' => $groupes->getId())),
            'method' => 'PUT',
        ));

        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_groups'));
        }
        $deleteForm = $this->createDeleteForm($groupes->getId(), 'admin_groups_delete');

        return $this->render('@App/Groupes/edit.html.twig', array(
            'groupes' => $groupes,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
    * @Route("/{id}/delete",name="admin_groups_delete", methods={"post","delete"})
    */

    public function deleteAction(Groupes $groupes, Request $request)
    {
        $form = $this->createDeleteForm($groupes->getId(), 'admin_groups_delete');
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($groupes);
            $manager->flush();
        }

        return $this->redirect($this->generateUrl('admin_groups'));
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
    * @Route("/{id}/add",name="admin_groups_add_roles")
    */

    public function add_rolesAction(Groupes $groupes, Request $request)
    {
        //$Role=new Roles();
        $formRole= $this->createForm(ListeRoleType::Class);
        //$request = $this->getRequest();
        if($request->getMethod() == 'POST')
        {
            $formRole->handleRequest($request);
            if($formRole->isValid())
    		{
                $manager = $this->getDoctrine()->getManager();
    		    $result = $request->get('liste_role');
    		    $roleId = $result['rolename'];
    		    $roles = $manager->getRepository('AppBundle:Roles')->find($roleId);
    		    $rolename = $roles->getRolename();

                $rolegroups=$groupes->getRoles();

                if ($rolegroups==NULL)
                {
                    $key=false;
                }else {
                    $key = array_search($rolename, $rolegroups);
                }

                if ($key === false)
                {
                    $rolegroups[] = $rolename;
                    $groupes->setRoles($rolegroups);
                    $manager->persist($groupes);
                    $manager->flush();
                    $this->get('session')->getFlashBag()->add('success', '-'.$rolename.'-  ajouter.');
                }else {
                    $this->get('session')->getFlashBag()->add('warning', '-'.$rolename.'- existe déjà.');
                }
//                $key = array_search($role, $roles);
//                unset($roles[$key]);
//                $roles = array_merge($roles);

    	   	}
    	 }

        $role = new Roles();
        $formRole = $this->createForm(ListeRoleType::Class, $role, array(
            'action' => $this->generateUrl('admin_groups_add_roles', array('id' => $groupes->getId())),
            'method' => 'POST',
        ));
        $deleteForm = $this->createDeleteForm($groupes->getId(), 'admin_groups_delete');
        return $this->render('@App/Groupes/show.html.twig', array(
            'groupes' => $groupes,
            'delete_form' => $deleteForm->createView(),
            'formRole' => $formRole->createView(),
            ));

    }
    

    /**
    * @Route("/{id}/{rolename}/delete",
    *     options = { "expose" = true },
    *     name = "admin_groups_delete_roles",
    * )
    */
    public function delete_rolesAction(Groupes $groupes, $rolename, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $rolegroups=$groupes->getRoles();

        $key = array_search($rolename, $rolegroups);

        if ($key === false)
        {
/*          $rolegroups[] = $rolename;
            $groupes->setRoles($rolegroups);
            $manager->persist($groupes);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success', '-'.$rolename.'-  ajouter.');
*/      }else {
            unset($rolegroups[$key]);
            $rolegroups = array_merge($rolegroups);
            $groupes->setRoles($rolegroups);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success', '-'.$rolename.'- est supprimer.');
        }

        $role = new Roles();
        $formRole = $this->createForm(ListeRoleType::Class, $role, array(
            'action' => $this->generateUrl('admin_groups_add_roles', array('id' => $groupes->getId())),
            'method' => 'POST',
        ));

        $deleteForm = $this->createDeleteForm($groupes->getId(), 'admin_groups_delete');
        return $this->render('@App/Groupes/show.html.twig', array(
            'groupes' => $groupes,
            'delete_form' => $deleteForm->createView(),
            'formRole' => $formRole->createView(),
            ));

    }


}

