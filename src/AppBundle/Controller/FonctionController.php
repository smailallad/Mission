<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Fonction;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\FonctionType;
use AppBundle\Form\FonctionFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/fonction")
 * @Security("has_role('ROLE_ADMINISTRATION')")
 */
class FonctionController extends Controller
{
    /**
     * @Route("/fonction",name="fonction")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(FonctionFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'fonction', 'fonction'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:Fonction')->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'fonction');
        $forme=$form->createView();
        return $this->render('@App/Fonction/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/new",name="fonction_new")
     */
    public function newAction(Request $request)
    {
        $fonction = new Fonction();
        $form = $this->createForm(FonctionType::class, $fonction);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($fonction);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('fonction_show', array('id' => $cryptage->my_encrypt($fonction->getId()))));
        }
        return $this->render('@App/Fonction/new.html.twig', array(
            'fonction' => $fonction,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="fonction_edit")
     * id : fonction
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $fonction = $this->getDoctrine()->getRepository('AppBundle:Fonction')->find($id);
        $editForm = $this->createForm(FonctionType::class, $fonction, array(
            'action' => $this->generateUrl('fonction_edit', array('id' => $cryptage->my_encrypt($fonction->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('fonction_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/Fonction/edit.html.twig', array(
            'fonction'          => $fonction,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/{id}/show",name="fonction_show")
     * id : fonction
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $fonction = $this->getDoctrine()->getRepository('AppBundle:Fonction')->find($id);
        $deleteForm = $this->createDeleteForm($id, 'fonction_delete');
        return $this->render('@App/Fonction/show.html.twig', array(
            'fonction'       => $fonction,
            'delete_form'   => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/delete",name="fonction_delete")
     *
     */
    public function deleteAction(Fonction $fonction, Request $request)
    {   $manager = $this->getDoctrine()->getManager();
        $manager->remove($fonction);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette fonction.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $fonction->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('fonction_show', array('id' => $id)));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('fonction'));
    }
    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="fonction_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('fonction', $field, $type);
        return $this->redirect($this->generateUrl('fonction'));
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
