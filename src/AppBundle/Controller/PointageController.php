<?php

namespace AppBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\PointageUser;
use AppBundle\Form\PointageUserType;
use AppBundle\Form\PointageFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
* @Route("/pointage")
* @Security("has_role('ROLE_ROLLOUT')")
*/

class PointageController extends Controller
{
    /**
     * @Route("/new",name="pointage_new")
     */
    public function newAction(Request $request)
    {   $pointageUser = new PointageUser();
        $form = $this->createForm(PointageUserType::class, $pointageUser);
        
        //if ($form->handleRequest($request)->isValid())
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            //dump($form);
            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($pointageUser);
                $manager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
                $cryptage = $this->container->get('my.cryptage');
                return $this->redirect($this->generateUrl('pointage_new'));
            }else{
                //dump('erreur Valide');
            }
        }else{
            //dump('non submit');
        }
        return $this->render('@App/PointageUser/new.html.twig', array(
            'pointageUser' => $pointageUser,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/show",name="pointage_show")
     */
    public function showAction()
    {
        
        return $this->render('@App/PointageUser/show.html.twig', array(
            // ...
        ));
   }

    /**
     * @Route("/index",name="pointage_index")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(PointageFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'pointage', 'pointage_index'))) {
            return $response;
        }
        //$qb = $manager->getRepository('AppBundle:PointageUser')->createQueryBuilder('p');
        if (is_null($values = $this->getFilter('pointage'))) {
            $qb = $manager->getRepository('AppBundle:PointageUser')->getPointages(1);
        }else{
            $qb = $manager->getRepository('AppBundle:PointageUser')->getPointages();
        } 
        
        $paginator = $this->filter($form, $qb, 'pointage');
        $forme=$form->createView();
        return $this->render('@App/PointageUser/index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        
        ));
    }
    /**
     * @Route("/{id}/edit",name="pointage_edit")
     * id : pointage
     */
    public function editAction($id, Request $request)
    {   $cryptage = $this->container->get('my.cryptage');
       $id = $cryptage->my_decrypt($id);
        $pointageUser = $this->getDoctrine()->getRepository('AppBundle:PointageUser')->find($id);
        $editForm = $this->createForm(PointageUserType::class, $pointageUser, array(
            'action' => $this->generateUrl('pointage_edit', array('id' => $cryptage->my_encrypt($pointageUser->getId()))),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('pointage_edit', array('id' => $cryptage->my_encrypt($id))));
        }
        return $this->render('@App/PointageUser/edit.html.twig', array(
            'site'          => $pointageUser,
            'edit_form'     => $editForm->createView(),
        ));
    }
    /**
     * @Route("/pointage_users/{date}",name="pointage_users",options = { "expose" = true })
     */
    public function pointageUserAction($date)
    {
        $pointageUser = $this->getDoctrine()->getRepository('AppBundle:User')->getNotPointageUsers($date);
        $pointageUser = $pointageUser->getQuery()->getResult();
        $pointageUsers =  $this->get('serializer')->serialize($pointageUser, 'json');
        //return $interventions;
        return $this->json(["users"     => $pointageUsers,
                            ],
                            200);
    }


    //*********************************************************************************//
    /**
    * @route("/{field}/{type}/sort",name="pointage_sort",requirements={ "type"="ASC|DESC" })
    */
    public function sortAction($field, $type)
    {
        $this->setOrder('pointage', $field, $type);
        return $this->redirect($this->generateUrl('pointage_index'));
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
    /*
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }*/
    protected function getOrder($name)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }


}
