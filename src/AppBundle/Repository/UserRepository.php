<?php

namespace AppBundle\Repository;

//use Doctrine\ORM\EntityRepository;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository implements UserLoaderInterface
{
     /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername($username) {

        return $this->createQueryBuilder('u')
                    ->where('u.username = :username OR u.email = :email')
                    ->setParameter('username',$username)
                    ->setParameter('email',$username)
                    ->getQuery()
                    ->getOneOrNullResult();


    }
    public function getNotRealisateursIntervention($intervention,$date,$mission)
    {
        $qb1 = $this->getEntityManager()->createQueryBuilder();
        $qb1    ->select('DISTINCT(iu.user)')
                ->from('AppBundle:InterventionUser', 'iu')
                ->where('iu.intervention = :v1')
                ;
        
        $qb3 = $this->getEntityManager()->createQueryBuilder();
        $qb3    ->select('DISTINCT(m.user)')
                ->from('AppBundle:Mission', 'm')
                ->where(':d BETWEEN m.depart AND m.retour')
                ;
        $qb4 = $this->getEntityManager()->createQueryBuilder();
        $qb4    ->select('DISTINCT(iuu.user)')
                ->from('AppBundle:InterventionUser', 'iuu')
                ->join('iuu.intervention','i')
                ->where('i.mission <>:mission')
                ->andWhere('i.dateIntervention =:d1')
                ;

        //return $qb1->getQuery()->getResult(); 

        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2    ->select('u')
                ->from('AppBundle:User', 'u')
                ->where('u.active = true')
                ->andWhere('u.mission = true')
                ->andWhere($qb1->expr()->notIn('u', $qb1->getDQL()))
                ->andWhere($qb3->expr()->notIn('u', $qb3->getDQL()))
                ->andWhere($qb4->expr()->notIn('u', $qb4->getDQL()))
                ->orderBy('u.nom')
                ->setParameter('v1',$intervention)
                ->setParameter('d', $date)
                ->setParameter('mission',$mission)
                ->setParameter('d1',$date)
        ;
        //dump($qb2);
        //throw new \Exception('Message');
        return $qb2;
        //return $qb2->getQuery()->getResult();
       
    }
    public function getChefMissionDate($date,$mission)
    {   // Liste des chef de mission entre depart au Retour
        
        $qb4 = $this->getEntityManager()->createQueryBuilder();
        $qb4    ->select('DISTINCT(iu.user)')
                ->from('AppBundle:InterventionUser', 'iu')
                ->join('iu.intervention','i')
                ->where('i.mission <>:mission')
                ->andWhere('i.dateIntervention =:d1')
                ->setParameter('mission',$mission)
                ->setParameter('d1',$date)
                ;
        /*
        $qb3 = $this->getEntityManager()->createQueryBuilder();
        $qb3    ->select('DISTINCT(m.user)')
                ->from('AppBundle:Mission', 'm')
                ->where(':d BETWEEN m.depart AND m.retour')
                ->setParameter('d', $date)
                ;
        */
        /*$qb3 = $this->getEntityManager()->createQueryBuilder();
        $qb3    ->select('m')
                ->from('AppBundle:Mission', 'm')
                ->where(':d BETWEEN m.depart AND m.retour')
                ->setParameter('d', $d)
                ;
        */
        return $qb4->getQuery()->getResult();
    }
}
