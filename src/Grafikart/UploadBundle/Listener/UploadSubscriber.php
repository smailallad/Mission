<?php

namespace Grafikart\UploadBundle\Listener;

use Doctrine\common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Grafikart\UploadBundle\Annotation\UploadAnnotationReader;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UploadSubscriber implements EventSubscriber {

  public function __construct(UploadAnnotationreader $reader) {
    $this->reader = $reader;
  }

	public function getSubscribedEvents() {
		
		return [
			'prePersist'
		];
	}
	
	public function prePersist(EventArgs $event) {
		
		$entity = $event->getEntity();
    foreach ($this->reader->getUploadableFields($entity) as $property => $annotation)	{
      $accessor = PropertyAccess::createPropertyAccessor();
      $file = $accessor->getValue($entity,$property);
    }	
    
    throw new \Exception("test");
	}

}