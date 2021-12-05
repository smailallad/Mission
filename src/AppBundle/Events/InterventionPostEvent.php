<?php
namespace AppBundle\Events;

use AppBundle\Entity\Contact;
use Doctrine\Common\EventArgs;

use Doctrine\Common\EventSubscriber;

class InterventionPostEvent implements EventSubscriber {

    public function getSubscribedEvents() {
        return array('prePersist', 'preUpdate');//les événements écoutés
    }

    public function prePersist(EventArgs $args) {
        //$entity = $args->getEntity();
        //throw new \Exception('prePersist');
        //Si c'est bien une entité Contact qui va être "persisté"
        //if ($entity instanceof Intervention) {
            //$entity->updateGmapData();//on met à jour les coordonnées via l'appel à google map
            //throw new \Exception('prePersist');
        //}
    }

    public function preUpdate(EventArgs $args) {
        /*$entity = $args->getEntity();
       
        $a = get_class($entity);
        $b = strripos($a,"\\");
        if ($b != false){
            $c= substr($a,$b+1);
        }else{
            $c=$a;
        }
        */
        
        //if ($c == 'Mission') {
            
           // throw new \Exception('preUpdate');
        /*}else
        {
            //throw new \Exception('preUpdate non entrer');

        }*/
    }

}
