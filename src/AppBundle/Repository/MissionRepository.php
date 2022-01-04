<?php

namespace AppBundle\Repository;

/**
 * MissionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MissionRepository extends \Doctrine\ORM\EntityRepository{
    
    public function getMissionAll($t){
        // t = M : Mission
        // t = C : Note de frais
        $q = $this->createQueryBuilder('m');
        $q  ->join('m.user','u')
            ->addSelect('u')
            ->where($q->expr()->eq($q->expr()->substring('m.code', 5,1),':v1'))
            ->orderby('m.code','DESC')
            ->setParameter('v1', $t)
            ;
        return $q;


        //return $q->getQuery()->getResult();

        /*$q = $this->createQueryBuilder('location')
            ->select('location')
            ->addSelect("GROUP_CONCAT(DISTINCT location.name SEPARATOR '; ') AS locationNames");

        $result = $queryBuilder->getQuery()->getResult();*/
    }
    public function getAvanceMission($t){
        // t = M : Mission
        // t = C : Note de frais
        $q = $this->createQueryBuilder('m');
        $q->select('SUM(m.avance) AS total');
        $q->where($q->expr()->eq($q->expr()->substring('m.code', 5,1),':v1'))
        ->setParameter('v1', $t)
        ;
        return $q;
    }

    public function getAvanceMissionUser($user,$t){
        // t = M : Mission
        // t = C : Note de frais
        $q = $this->createQueryBuilder('m');
        $q->select('SUM(m.avance) AS total');
        $q->where($q->expr()->eq($q->expr()->substring('m.code', 5,1),':v1'));
        $q->andWhere('m.user = :user');
        $q->andWhere('m.vComptabilite = 0');
        $q->setParameter('v1', $t);
        $q->setParameter('user',$user);

        return $q->getQuery()->getResult(); 
    }

    public function getMissionUser($user,$t){
        // t = M : Mission
        // t = C : Note de frais
        $q = $this->createQueryBuilder('m');
        $q ->join('m.user','u')
           ->addSelect('u');
        $q ->where('m.user = :user');
        $q ->andWhere($q->expr()->eq($q->expr()->substring('m.code', 5,1),':v1'))
           ->andwhere('m.vComptabilite = false')
           ->orderby('m.code','DESC')
           ->setParameter('user', $user)
           ->setParameter('v1', $t)
           ;
        return $q->getQuery()->getResult();

    }
    public function getNextMission($annee){
        $annee = $annee."M";
        $q = $this->createQueryBuilder('m');
        $q ->select($q->expr()->max('m.code') );
        $q ->where($q->expr()->like('m.code', $q->expr()->literal($annee.'%')));
        $res = $q->getQuery()->getResult()[0][1];
        if ($res ===null)
        {
            $res = $annee."0001";
        }
        else
        {
            $m=$res;
            $m=substr($m,-4);
            $m=intval($m)+1;
            $m=str_repeat("0",4-strlen($m)).$m;
            $res = $annee.$m;
        }
        return $res;
    }
    
    public function getNextNoteFrais($annee){
        $annee = $annee."C";
        $q = $this->createQueryBuilder('m');
        $q ->select($q->expr()->max('m.code') );
        $q ->where($q->expr()->like('m.code', $q->expr()->literal($annee.'%')));
        $res = $q->getQuery()->getResult()[0][1];
        if ($res = null)
        {
            $res = $annee."0001";
        }
        else
        {
            $m=$res;
            $m=substr($m,-4);
            $m=intval($m)+1;
            $m=str_repeat("0",4-strlen($m)).$m;
            $res = $annee.$m;
        }
        return $res;
    }

    public function getMission(){
        $em = $this->getEntityManager();
        /*$query = $em->createQuery("
        SELECT
            m.id,m.code,m.avance,m.code,m_dm,m_fm,(m_dm + m_fm) as total_depense,(m.avance - m_dm - m_fm) as solde
        FROM AppBundle:Mission as m
        LEFT JOIN (
            SELECT mission, SUM(montant) AS m_dm
            FROM AppBundle:DepenseMission
            GROUP BY mission
            ) as mf
            ON m = mf.mission
        LEFT JOIN (
            SELECT mission, SUM(montant) AS m_fm
            FROM AppBundle:FraisMission
            GROUP BY mission
            ) as md
            ON m = md.mission"
        );
        */
  /*      $query = $em->createQuery(" SELECT m.id,m.code,m.avance,m.code, SUM(dm.montant) as depense,SUM(fm.montant) as mfm
                                    FROM AppBundle:Mission m
                                    LEFT JOIN  AppBundle:DepenseMission as dm
                                    with m.id = dm.mission
                                    GROUP BY m.id
                                    INNER JOIN  AppBundle:FraisMission as fm
                                    with m.id = fm.mission
                                    GROUP BY m.id   
                                    ");
       
        $res = $query->getResult();
        return $res;
*/

 /*     $query = $em->createQuery("   SELECT m.id,m.code,m.avance,m.code, SUM(dm.montant) as depense,SUM(fm.montant) as mfm
                                    FROM AppBundle:Mission m
                                    LEFT JOIN  AppBundle:DepenseMission as dm
                                    with m.id = dm.mission
                                    INNER JOIN  AppBundle:FraisMission as fm
                                    with m.id = fm.mission
                                    GROUP BY m.id  
                                    ");
        
        $res = $query->getResult();
        return $res;
        */

/*        $query = $em->createQueryBuilder('m')
        ->from('AppBundle:Mission', 'm')
        ->select('m.id,m.code,m.avance,m, SUM(dm.montant) as depense,SUM(fm.montant) as mfm')
        ->leftJoin('AppBundle:DepenseMission', 'dm','WITH','dm.mission = m' )
        ->leftJoin('AppBundle:FraisMission', 'fm', 'WITH',' fm.mission = m')
        ->where('m.code = :code')
        ->groupBy('m')
        ->setParameter('code', '2021M0001');
        ;
        $res = $query->getQuery()->execute();
        return $res;
*/


         //WHERE m.code LIKE ':id%'");
        //$query->setParameter(1, $id);
        
    }

   
}
