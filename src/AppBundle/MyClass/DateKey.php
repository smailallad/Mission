<?php
namespace AppBundle\MyClass;
class DateKey extends \DateTime
{
    function __toString()
    {
        return $this->format('c');
    }
   /* static function fromDateTime(\DateTime $dateTime)
    {
        return new static($dateTime->format('c'));
    }*/
}
