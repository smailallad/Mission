<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Test
 *
 * @ORM\Table(name="test")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TestRepository")
 * @UniqueEntity(fields={"date"},message="Date existe")
 */
class Test
{
    
    /**
     * @var \DateTime
     * @ORM\Id
     * @ORM\Column(name="date", type="string")
     */
    private $date;


    /**
     * Set date
     *
     * @param string $date
     *
     * @return Test
     */
    public function setDate($date)
    {
        $this->date = $date->format("Y-m-d");

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return new DateTime($this->date);
    }
}
