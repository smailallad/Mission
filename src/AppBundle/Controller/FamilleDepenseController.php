<?php
namespace AppBundle\Controller;
use AppBundle\Entity\FamilleDepense;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\FamilleDepenseType;
use AppBundle\Form\FamilleDepenseFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/famille/depense")
 * @Security("has_role('ROLE_ADMINISTRATION')")
 */
class FamilleDepenseController extends Controller
{
    /**
     * @Route("/familleDepense",name="familleDepense")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(FamilleDepenseFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'familleDepense', 'familleDepense'))) {
            return $response;
        }
        $qb = $manager->getRepository('AppBundle:FamilleDepense')->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'familleDepense');
        $forme=$form->createView();
        return $this->render('@App/FamilleDepense/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }
    /**
     * @Route("/new",name="familleDepense_new")
     */
    public function newAction(Request $request)
    {
        $familleDepense = new FamilleDepense();
        $form = $this->createForm(FamilleDepenseType::class, $familleDepense);
        if ($form->handleRequest($request)->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($familleDepense);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect($this->generateUrl('familleDepense_show', array('id' => $cryptage->my_encrypt($familleDepense->getId()))));
        }
        return $this->render('@App/FamilleDepense/new.html.twig', array(
            'familleDepense' => $familleDepense,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/edit",name="familleDepense_edit")
     * id : familleDepense
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $familleDepense = $this->getDoctrine()->getRepository('AppBundle:FamilleDepense')->find($id);
        $editForm = $this->createForm(FamilleDepenseType::class, $familleDepense, array(
            'action' => $this->generateUrl('familleDepense_edit', array('id' => $cryptage->my_encrypt($familleDepense->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('familleDepense_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/FamilleDepense/edit.html.twig', array(
            'familleDepense'          => $familleDepense,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/{id}/show",name="familleDepense_show")
     * id : familleDepense
     */
    public function showAction($id)
    {   $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $familleDepense = $this->getDoctrine()->getRepository('AppBundle:FamilleDepense')->find($id);
        $deleteForm = $this->createDeleteForm($id, 'familleDepense_delete');
        return $this->render('@App/FamilleDepense/show.html.twig', array(
            'familleDepense'       => $familleDepense,
            'delete_form'   => $deleteForm->createView()));
    }
    /**
     * @Route("/{id}/delete",name="familleDepense_delete")
     *
     */
    public function deleteAction(FamilleDepense $familleDepense, Request $request)
    {   $manager = $this->getDoctrine()->getManager();
        $manager->remove($familleDepense);
        try {
            $manager->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette familleDepense.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $familleDepense->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect($this->generateUrl('familleDepense_show', array('id' => $id)));
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('familleDepense'));
    }
    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="familleDepense_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('familleDepense', $field, $type);
        return $this->redirect($this->generateUrl('familleDepense'));
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
