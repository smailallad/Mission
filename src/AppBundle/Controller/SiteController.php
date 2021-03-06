<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Site;
use AppBundle\Form\SiteType;
use AppBundle\Form\Site1Type;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\SiteFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/site")
 * @Security("has_role('ROLE_ROLLOUT')")
 */
class SiteController extends Controller
{
    /** 
     * @Route("/site",name="site")
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(SiteFilterType::class);
        if (!is_null($response = $this->saveFilter($form, 'site', 'site'))) {
            return $response;
        }
        $qb = $manager
            ->getRepository('AppBundle:Site') 
            ->createQueryBuilder('s');
        $paginator = $this->filter($form, $qb, 'site');
        
        return $this->render('@App/Site/index.html.twig', [
            'form' => $form->createView(),
            'paginator' => $paginator,
        ]);
    }
    /**
     * @Route("/new",name="site_new")
     */
    public function newAction(Request $request)
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($site);
            $manager->flush();
            //$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            $cryptage = $this->container->get('my.cryptage');
            return $this->redirect(
                $this->generateUrl('site_show', [
                    'id' => $cryptage->my_encrypt($site->getId()),
                ])
            );
        }
        return $this->render('@App/Site/new.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit",name="site_edit")
     * id : site
     */
    public function editAction($id, Request $request)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id         = $cryptage->my_decrypt($id);
        $site       = $this->getDoctrine()->getRepository('AppBundle:Site')->find($id);
        $intervention = $this->getDoctrine()->getRepository('AppBundle:Intervention')->findOneBySite($site);
        // si le site est deja figuré dans une intervention interdire de modifier la zone, la wilaya et le client
        if ($intervention != null)
        {
            $editForm   = $this->createForm(Site1Type::class, $site, [
                'action'    => $this->generateUrl('site_edit', [
                'id'        => $cryptage->my_encrypt($site->getId()),
                ]),
                'method' => 'PUT',
            ]);
            $readonly = true;
        }else
        {
            $editForm   = $this->createForm(SiteType::class, $site, [
                'action'    => $this->generateUrl('site_edit', [
                'id'        => $cryptage->my_encrypt($site->getId()),
                ]),
                'method' => 'PUT',
            ]);
            $readonly = false;
        }
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectuer avec sucées.');
            return $this->redirect($this->generateUrl('site_edit', ['id' => $cryptage->my_encrypt($id)]));
        }
        if ($readonly)
        {
            return $this->render('@App/Site/edit1.html.twig', [
                'site'      => $site,
                'edit_form' => $editForm->createView(),
                ]);
        }else
        {
            return $this->render('@App/Site/edit.html.twig', [
                'site'      => $site,
                'edit_form' => $editForm->createView(),
                ]);
        }
    }
    /**
     * @Route("/{id}/show",name="site_show")
     * id : site
     */
    public function showAction($id)
    {
        $cryptage = $this->container->get('my.cryptage');
        $id = $cryptage->my_decrypt($id);
        $site = $this->getDoctrine()
            ->getRepository('AppBundle:Site')
            ->find($id);
        $deleteForm = $this->createDeleteForm($id, 'site_delete');
        return $this->render('@App/Site/show.html.twig', [
            'site' => $site,
            'delete_form' => $deleteForm->createView(),
        ]);
    }
    /**
     * @Route("/{id}/delete",name="site_delete")
     *
     */
    public function deleteAction(Site $site, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($site);
        try {
            $manager->flush();
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', 'Impossible de supprimer cet element.');
            $cryptage = $this->container->get('my.cryptage');
            $id = $site->getId();
            $id = $cryptage->my_encrypt($id);
            return $this->redirect(
                $this->generateUrl('site_show', ['id' => $id])
            );
        }
        //$this->get('session')->getFlashBag()->add('success', 'Suppression avec succès.');
        return $this->redirect($this->generateUrl('site'));

        /** */

        /*        $form = $this->createDeleteForm($site->getId(), 'site_delete');
        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($site);
            try {
            $manager->flush();
            } catch(\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('danger', 'Impossible de supprimer cette site.');
                $cryptage = $this->container->get('my.cryptage');
                $id = $site->getId();
                $id = $cryptage->my_encrypt($id);
                return $this->redirect($this->generateUrl('site_show', array('id' => $id)));
            }
        }
        return $this->redirect($this->generateUrl('site'));*/
    }

    /**
     * @Route("/c/{client}/p/{pagenum}/s/{site}",name="search_site_client",options = { "expose" = true })
     */
    public function searchSiteClientAction($client,$pagenum,$site=null,SerializerInterface $serializer)
    {
        $manager = $this->getDoctrine()->getManager();
        $maxRows = 10 ;
        $startRow = $pagenum * $maxRows;
        $totalRows = $manager->getRepository("AppBundle:Site")->getTotalRowsSiteClient($client,$site);
        $totalpages = ceil($totalRows/$maxRows)-1;
        $sites = $manager->getRepository("AppBundle:Site")->getSitess($client,$site,$startRow,$maxRows);
        $sites = $sites->getQuery()->getResult();

        $sites = $serializer->serialize(
            $sites,
            'json',
            ['groups' => ['site_json']]
        );
        return $this->json(["sites"     => $sites,
                            "pagenum"   => $pagenum,
                            "totalpages"=> $totalpages,
                            ],
                            200);
    }
    
    /**
     * @Route("/c/{client}/p/{pagenum}/z/{zone}/s/{site}",name="search_site_client_zone",options = { "expose" = true })
     */
    public function searchSiteClientZoneAction($client,$pagenum,$zone,$site=null,SerializerInterface $serializer)
    {
        $manager = $this->getDoctrine()->getManager();
        $maxRows = 10 ;
        $startRow = $pagenum * $maxRows;
        $totalRows = $manager->getRepository("AppBundle:Site")->getTotalRowsSiteClientZone($client,$site,$zone);

        $totalpages = ceil($totalRows/$maxRows)-1;
        $sites = $manager->getRepository("AppBundle:Site")->getSiteClientZone($client,$site,$zone,$startRow,$maxRows);
        $sites = $sites->getQuery()->getResult();
        
        $sites = $serializer->serialize(
            $sites,
            'json',
            ['groups' => ['site_json']]
        );
        return $this->json(["sites"     => $sites,
                            "pagenum"   => $pagenum,
                            "totalpages"=> $totalpages,
                            ],
                            200);
    }
    /**
     * @route("/{field}/{type}/sort",name="site_sort",requirements={ "type"="ASC|DESC" })
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('site', $field, $type);
        return $this->redirect($this->generateUrl('site'));
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
        return $this->createFormBuilder(null, ['attr' => ['id' => 'delete']])
            ->setAction($this->generateUrl($route, ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm();
    }
    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $request
            ->getSession()
            ->set('sort.' . $name, ['field' => $field, 'type' => $type]);
    }
    protected function saveFilter(FormInterface $form,$name,$route = null,array $params = null) 
    {
        //$request = $this->getRequest();
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $url = $this->generateUrl(
            $route ?: $name,
            is_null($params) ? [] : $params
        );
        if (
            $request->query->has('submit-filter') &&
            $form->handleRequest($request)->isValid()
        ) {
            $request
                ->getSession()
                ->set(
                    'filter.' . $name,
                    $request->query->get($form->getName())
                );
            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);
            return $this->redirect($url);
        }
    }
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {dump($qb->getDQL());
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }
        // possible sorting
        // nombre de ligne
        $session = $this->get('session');
        $nbr_pages = $session->get('nbr_pages');
        if ($nbr_pages == null) {
            $nbr_pages = 20;
        }
        $this->addQueryBuilderSort($qb, $name);
        $request = $this->container->get('request_stack')->getCurrentRequest();
        return $this->get('knp_paginator')->paginate($qb,$request->query->get('page', 1),$nbr_pages);
    }
    protected function getFilter($name)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
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
        return $session->has('sort.' . $name)
            ? $session->get('sort.' . $name)
            : null;
    }
}
