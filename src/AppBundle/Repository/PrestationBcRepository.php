<?php

namespace AppBundle\Repository;

/**
 * PrestationBcRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestationBcRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSomme($bc)
    {   
        $q = $this->createQueryBuilder('p')
        ->select('SUM(p.montant * p.quantite) AS somme')
        ->where('p.bc = :bc')
        ->setParameter('bc',$bc);
        ;
        //return $q->getQuery()->getResult()[0];
        return $q->getQuery()->getSingleScalarResult();

    }

    public function getPrestationIntervention($bc,$prestation,$zone,$site)
    {
        $q = $this->createQueryBuilder('pb')
            ->join('pb.bc','bc')
            ->join('pb.prestation','p')
            ->join('pb.zone','z')
            ->where('bc = :bc')
            ->andWhere('p = :prestation')
            ->andWhere('z = :zone')
            ->setParameter('bc',$bc)
            ->setParameter('prestation',$prestation)
            ->setParameter('zone',$zone);
            if ($site != null)
            {   
                $q  ->andWhere('pb.site = :site')
                    ->setParameter('site',$site)
                ;
                
            }
    
            ;
        return $q->getQuery()->getResult();

    }

    public function getSitesBc($bc)
    {
        $q = $this  ->createQueryBuilder('p')
                    ->join('p.site','s')
                    ->where('p.bc =:bc')
                    ->setParameter('bc',$bc)
        ;
        return $q->getQuery()->getResult();
    }
    

}
