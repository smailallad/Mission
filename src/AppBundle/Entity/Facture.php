<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Facture
 *
 * @ORM\Table(name="facture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactureRepository")
 */
class Facture
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
     * @ORM\Column(name="num", type="string", length=255, unique=true)
     */
    private $num;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float",nullable=false)
     */
    private $tva;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Bc",inversedBy="factures")
     * @JoinColumn(name="bc_id", referencedColumnName="id")
     */
    private $bc;

    /** 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Intervention",mappedBy="facture") 
     */
    private $interventions; 


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set num
     *
     * @param string $num
     *
     * @return Facture
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Facture
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Facture
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set bc.
     *
     * @param \AppBundle\Entity\Bc|null $bc
     *
     * @return Facture
     */
    public function setBc(\AppBundle\Entity\Bc $bc = null)
    {
        $this->bc = $bc;

        return $this;
    }

    /**
     * Get bc.
     *
     * @return \AppBundle\Entity\Bc|null
     */
    public function getBc()
    {
        return $this->bc;
    }

    /**
     * Set tva.
     *
     * @param float $tva
     *
     * @return Facture
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva.
     *
     * @return float
     */
    public function getTva()
    {
        return $this->tva;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->interventions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add intervention.
     *
     * @param \AppBundle\Entity\Intervention $intervention
     *
     * @return Facture
     */
    public function addIntervention(\AppBundle\Entity\Intervention $intervention)
    {
        $this->interventions[] = $intervention;

        return $this;
    }

    /**
     * Remove intervention.
     *
     * @param \AppBundle\Entity\Intervention $intervention
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeIntervention(\AppBundle\Entity\Intervention $intervention)
    {
        return $this->interventions->removeElement($intervention);
    }

    /**
     * Get interventions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterventions()
    {
        return $this->interventions;
    }
}
