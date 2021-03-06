<?php

namespace AppBundle\Repository;

/**
 * CarburanMissiontRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CarburanMissiontRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCarburantMissions($mission)
    {   
        $q = $this->createQueryBuilder('cm');
        $q  ->select('cm')
            ->join('cm.carburant','c')
            ->addSelect('c')
            ->join('cm.vehicule','v')
            ->addSelect('v')
            ->join('cm.justificationDepense','jd')
            ->addSelect('jd');
        $q  ->where('cm.mission = :mm');
        $q  ->orderby('cm.date','ASC')
            ->setParameter('mm', $mission);
        return $q->getQuery()->getResult();
    }
    public function getMontantCarburantMissions($mission)
    {   
        $q = $this->createQueryBuilder('e')
        ->select('SUM(e.montant) AS total')
        ->where('e.mission = :mission')
        ->setParameter('mission',$mission);
        ;
        return $q->getQuery()->getResult()[0];
    }
 
    public function getTotalCarburantMissions($t){   
        // t = M : Mission
        // t = C : Note de frais
        $q = $this->createQueryBuilder('c')
            ->join('c.mission','m')
            ->join('m.user','u')
            ->select('SUM(c.montant) AS total');
        $q  ->where($q->expr()->eq($q->expr()->substring('m.code', 5,1),':v1'));
        $q  ->setParameter('v1', $t);

        return $q;
    }

    public function addFilterTotalCarburantMissions($q,$code,$depart,$retour,$user,$vEmploye,$vRollout,$vComptabilite){   
        
        if ($code !== null){
            $q->andWhere($q->expr()->like('m.code', $q->expr()->literal($code .'%')));
        }
        if ($depart !== null){
            $q->andWhere('m.depart >= :d1');
            $q->setParameter('d1',$depart);
        }
        if ($retour !== null){
            $q->andWhere('m.depart <= :d2');
            $q->setParameter('d2',$retour);
        }
        if ($user !== null){
            $q->andWhere('m.user = :user');
            $q->setParameter('user',$user);
        }
        if ($vEmploye !== null){
            $q->andWhere('m.vEmploye = :employe');
            $q->setParameter('employe',$vEmploye);
        }
        if ($vRollout !== null){
            $q->andWhere('m.vRollout = :rollout');
            $q->setParameter('rollout',$vRollout);
        }
        if ($vComptabilite !== null){
            $q->andWhere('m.vComptabilite = :comptabilite');
            $q->setParameter('comptabilite',$vComptabilite);
        }

        return $q;
    }

}
