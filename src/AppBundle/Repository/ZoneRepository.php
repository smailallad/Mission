<?php

namespace AppBundle\Repository;

/**
 * ZoneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ZoneRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAddZone($prestation){

        $qb1 = $this->getEntityManager()->createQueryBuilder();
        $qb1    ->select('IDENTITY(tp.zone)')
                ->from('AppBundle:TarifPrestation', 'tp')
                ->where('tp.prestation = :v1')
                ;

        //return $qb1->getQuery()->getResult(); 

        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2    ->select('z')
                ->from('AppBundle:Zone', 'z')
                //->where('u.active = true')
                //->andWhere('u.mission = true')
                ->where($qb1->expr()->notIn('z', $qb1->getDQL()))
                ->orderBy('z.nom')
                ->setParameter('v1',$prestation)
        ;
        //dump($qb2);
        //throw new \Exception('Message');
        return $qb2;
        //return $qb2->getQuery()->getResult();
       
    }
}
