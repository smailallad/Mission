<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Controletech
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ControletechRepository")
 * @UniqueEntity(fields={"dateDebut","vehicule"}, message="Controle technique de ce vehicule existe déjà avec cette date.")
 */
class ControlTech
{
		/**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vehicule",cascade="persist")
    */
		private $vehicule;

    /**
		 * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

		/**
 		 * @Assert\Date(message="Date incorrecte.")
     * @ORM\Column(type="date",nullable=false)
     */
    private $dateDebut;

		/**
 		 * @Assert\Date(message="Date incorrecte.")
     * @ORM\Column(type="date",nullable=false)
     */
    private $dateFin;

     /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $dernier;

    /**
	   * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $obs;


    public function __construct()
    {
      $this->dateDebut = new \Datetime();
		}

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
     * Set dateDebut.
     *
     * @param \DateTime $dateDebut
     *
     * @return ControlTech
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut.
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin.
     *
     * @param \DateTime $dateFin
     *
     * @return ControlTech
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin.
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dernier.
     *
     * @param int|null $dernier
     *
     * @return ControlTech
     */
    public function setDernier($dernier = null)
    {
        $this->dernier = $dernier;

        return $this;
    }

    /**
     * Get dernier.
     *
     * @return int|null
     */
    public function getDernier()
    {
        return $this->dernier;
    }

    /**
     * Set obs.
     *
     * @param string|null $obs
     *
     * @return ControlTech
     */
    public function setObs($obs = null)
    {
        $this->obs = $obs;

        return $this;
    }

    /**
     * Get obs.
     *
     * @return string|null
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * Set vehicule.
     *
     * @param \AppBundle\Entity\Vehicule|null $vehicule
     *
     * @return ControlTech
     */
    public function setVehicule(\AppBundle\Entity\Vehicule $vehicule = null)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule.
     *
     * @return \AppBundle\Entity\Vehicule|null
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }
}