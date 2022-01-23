<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Projet;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\ProjetType;
use AppBundle\Form\ProjetFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/projet")
 * @Security("has_role('ROLE_FACTURATION')")
 */
class ProjetController extends Controller
{
    /**
     * @Route("/projet",name="projet")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProjetFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'projet', 'projet'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Projet')->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'projet');
        $forme=$form->createView();
        return $this->render('@App/Projet/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/new",name="projet_new")
     */
    public function newAction(Request $request)
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($projet);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('projet'));
        }
        return $this->render('@App/Projet/new.html.twig', array(
            'projet' => $projet,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="projet_edit")
     * id : projet
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $projet = $this->getDoctrine()->getRepository('AppBundle:Projet')->find($id);
        $editForm = $this->createForm(ProjetType::class, $projet, array(
            'action' => $this->generateUrl('projet_edit', array('id' => $cryptage->my_encrypt($projet->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('projet'));
        }
        return $this->render('@App/Projet/edit.html.twig', array(
            'projet'          => $projet,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/{id}/show",name="projet_show")
     * id : projet
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $projet = $this->getDoctrine()->getRepository('AppBundle:Projet')->find($id);
        $deleteForm = $this->createDeleteForm($id, 'projet_delete');
        return $this->render('@App/Projet/show.html.twig', array(
            'projet'       => $projet,
            'delete_form'   => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/delete",name="projet_delete")
     *
     */
    public function deleteAction(Projet $projet, Request $request)
    {   $manager = $this->getDoctrine()->getManager();
        $manager->remove($projet);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette projet.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $projet->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('projet_show', array('id' => $id)));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('projet'));
    }
    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="projet_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('projet', $field, $type);
        return $this->redirect($this->generateUrl('projet'));
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
