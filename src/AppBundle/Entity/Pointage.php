<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pointage
 *
 * @ORM\Table(name="pointage")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PointageRepository")
 */
class Pointage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, unique=true)
     */
    private $designation;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set designation.
     *
     * @param string $designation
     *
     * @return Pointage
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation.
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Pointage
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    public function __toString()
    {
        return $this->designation;
    }

}
