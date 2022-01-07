<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Carburant
 *
 * @ORM\Table(name="carburant_mission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CarburanMissiontRepository")
 */
class CarburantMission
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
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mission")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mission;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vehicule")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vehicule;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carburant")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $carburant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JustificationDepense")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
    */
    private $justificationDepense;

    /**
     * @var integer
     * @ORM\Column(name="kms", type="integer")
     * 
     */
    private $kms;

     /**
     * @var float
     * @ORM\Column(name="montant", type="float")
     * 
     */
    private $montant;

    /**
     * @var date
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="string", length=255, nullable = true)
     */
    private $obs;
    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return CarburantMission
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CarburantMission
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
     * Set obs
     *
     * @param string $obs
     *
     * @return CarburantMission
     */
    public function setObs($obs)
    {
        $this->obs = $obs;

        return $this;
    }

    /**
     * Get obs
     *
     * @return string
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return CarburantMission
     */
    public function setMission(\AppBundle\Entity\Mission $mission)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \AppBundle\Entity\Mission
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set carburant
     *
     * @param \AppBundle\Entity\Carburant $carburant
     *
     * @return CarburantMission
     */
    public function setCarburant(\AppBundle\Entity\Carburant $carburant)
    {
        $this->carburant = $carburant;

        return $this;
    }

    /**
     * Get carburant
     *
     * @return \AppBundle\Entity\Carburant
     */
    public function getCarburant()
    {
        return $this->carburant;
    }

    /**
     * Set kms
     *
     * @param integer $kms
     *
     * @return CarburantMission
     */
    public function setKms($kms)
    {
        $this->kms = $kms;

        return $this;
    }

    /**
     * Get kms
     *
     * @return integer
     */
    public function getKms()
    {
        return $this->kms;
    }

    /**
     * Set vehicule
     *
     * @param \AppBundle\Entity\Vehicule $vehicule
     *
     * @return CarburantMission
     */
    public function setVehicule(\AppBundle\Entity\Vehicule $vehicule)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return \AppBundle\Entity\Vehicule
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set justificationDepense
     *
     * @param \AppBundle\Entity\JustificationDepense $justificationDepense
     *
     * @return CarburantMission
     */
    public function setJustificationDepense(\AppBundle\Entity\JustificationDepense $justificationDepense)
    {
        $this->justificationDepense = $justificationDepense;

        return $this;
    }

    /**
     * Get justificationDepense
     *
     * @return \AppBundle\Entity\JustificationDepense
     */
    public function getJustificationDepense()
    {
        return $this->justificationDepense;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return CarburantMission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
