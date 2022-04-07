<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;

/**
 * PrestationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestationRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPrestations($projet,$prestation,$startRow,$maxRows)
    {   
        $q = $this->createQueryBuilder('p');
        $q  ->select('p.id','p.nom')
            ->join('p.projet','pr')
            ->addSelect('pr.nom as projet');
        $q  ->where('p.projet = :projet');
        if ($prestation !== null)
        {
            $q  ->andWhere($q->expr()->like('p.nom', $q->expr()->literal('%'.$prestation.'%')));
        }
        $q  ->setFirstResult( $startRow )
            ->setMaxResults( $maxRows );
        $q  ->orderby('p.nom','ASC')
            ->setParameter('projet', $projet);
        return $q;
    }
    public function getTotalRows($projet,$prestation)
    {   
        $q = $this->createQueryBuilder('p')
            ->select('count(p)')
            ->join('p.projet','pr')
            ->where('p.projet = :projet');
            if ($prestation !== null)
            {
                $q  ->andWhere($q->expr()->like('p.nom', $q->expr()->literal('%'.$prestation.'%')));
            }
        $q  ->setParameter('projet', $projet);
        $q = $q->getQuery()->getResult(Query::HYDRATE_SINGLE_SCALAR);
        return $q;
    }
    public function getPrestationsProjet($projet)
    {   
        $q = $this->createQueryBuilder('p');
        $q  ->join('p.projet','pr')
            ->andwhere('p.projet = :projet')
            ->setParameter('projet', $projet)
            ->orderBy('p.nom')
            ;
        return $q;
    }
}
