<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterventionVehiculeRepository")
 * @UniqueEntity(fields="designation", message="La designation existe déjà avec ce nom.")
 */
class InterventionVehicule
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $unite;

    /**
     * @ORM\Column(type="boolean",nullable=false)
     */
    private $important;

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
     * @return InterventionVehicule
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
     * Set unite.
     *
     * @param string $unite
     *
     * @return InterventionVehicule
     */
    public function setUnite($unite)
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite.
     *
     * @return string
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return InterventionVehicule
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

    /**
     * Set important.
     *
     * @param bool|null $important
     *
     * @return InterventionVehicule
     */
    public function setImportant($important = null)
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Get important.
     *
     * @return bool|null
     */
    public function getImportant()
    {
        return $this->important;
    }
}
