<?php
namespace AppBundle\Controller;
use AppBundle\Entity\SousProjet;
use AppBundle\Form\SousProjetType;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\SousProjetFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/sousProjet")
 * @Security("has_role('ROLE_ROLLOUT')")
 */
class SousProjetController extends Controller
{
    /**
     * @Route("/sousProjet",name="sousProjet")
     */
    public function indexAction()
    {   
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(SousProjetFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'sousProjet', 'sousProjet'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:SousProjet')->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'sousProjet');
        $forme=$form->createView();
        return $this->render('@App/SousProjet/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/new",name="sousProjet_new")
     */
    public function newAction(Request $request)
    {
        $sousProjet = new SousProjet();
        $form = $this->createForm(SousProjetType::class, $sousProjet);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($sousProjet);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('sousProjet_show', array('id' => $cryptage->my_encrypt($sousProjet->getId()))));
        }
        return $this->render('@App/SousProjet/new.html.twig', array(
            'sousProjet' => $sousProjet,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="sousProjet_edit")
     * id : sousProjet
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $sousProjet = $this->getDoctrine()->getRepository('AppBundle:SousProjet')->find($id);
        $editForm = $this->createForm(SousProjetType::class, $sousProjet, array(
            'action' => $this->generateUrl('sousProjet_edit', array('id' => $cryptage->my_encrypt($sousProjet->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('sousProjet_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/SousProjet/edit.html.twig', array(
            'sousProjet'          => $sousProjet,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/{id}/show",name="sousProjet_show")
     * id : sousProjet
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $sousProjet = $this->getDoctrine()->getRepository('AppBundle:SousProjet')->find($id);
        $deleteForm = $this->createDeleteForm($id, 'sousProjet_delete');
        return $this->render('@App/SousProjet/show.html.twig', array(
            'sousProjet'          => $sousProjet,
            'delete_form'   => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/delete",name="sousProjet_delete")
     *
     */
    public function deleteAction(SousProjet $sousProjet, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($sousProjet);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $sousProjet->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('sousProjet_show', array('id' => $id)));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('sousProjet'));


        /** */

/*        $form = $this->createDeleteForm($sousProjet->getId(), 'sousProjet_delete');
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($sousProjet);
            try {
            $manager->flush();
            } catch(\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette sousProjet.');
                $cryptage = $this->container->get('my.cryptage');
                $id = $sousProjet->getId();
                $id = $cryptage->my_encrypt($id);
                return $this->redirect($this->generateUrl('sousProjet_show', array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl('sousProjet'));*/
    }
    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="sousProjet_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('sousProjet', $field, $type);
        return $this->redirect($this->generateUrl('sousProjet'));
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
