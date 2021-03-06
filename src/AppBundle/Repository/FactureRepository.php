<?php

namespace AppBundle\Repository;

/**
 * FactureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FactureRepository extends \Doctrine\ORM\EntityRepository
{
    public function listeFactures()
    {
        $q = $this->createQueryBuilder('f')
                ->join('f.bc','bc')
        ;
        return $q;
    }
    public function getMinDate($bc)
    {
        $q = $this->createQueryBuilder('f')
                ->select('MIN (f.date) AS dateMin')
                ->where('f.bc = :bc')
                ->setParameter('bc',$bc)
                ->getQuery()->getSingleScalarResult()
        ;

        return $q;
    }
}
