<?php

namespace AppBundle\Repository;

/**
 * PointageUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PointageUserRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPointages($v = null)
    {   
        $q = $this->createQueryBuilder('p');
        $q  ->join('p.user','u')
            ->join('p.pointage','pp')
            ;
        if ($v==1)
        {
            $d = new \DateTime("now");
            $q->where('p.date = :d');
            $q->setParameter('d', $d);
        }
            
        return $q;//->getQuery()->getResult();
    }

    public function nonPointer($d)
    {
        $qb1 = $this->getEntityManager()->createQueryBuilder();
        $qb1    ->select('DISTINCT(pu.user)')
                ->from('AppBundle:PointageUser', 'pu')
                ->where('pu.date = :d')
                ;
        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2    ->select('u')
                ->from('AppBundle:User', 'u')
                ->where('u.active = true')
                ->andWhere($qb1->expr()->notIn('u', $qb1->getDQL()))
                ->orderBy('u.nom')
                ->setParameter('d', $d)
        ;
        return $qb2->getQuery()->getResult();
    }

    public function addFilterPointage($q,$user,$du,$au){
        
        
        if ($du !== null){
            $q->andWhere('p.date >= :du');
            $q->setParameter('du',$du);
        }
        if ($au !== null){
            $q->andWhere('p.date <= :au');
            $q->setParameter('au',$au);
        }
        if ($user !== null){
            $q->andWhere('u = :user');
            $q->setParameter('user',$user);
        }
        return $q;
    }
}