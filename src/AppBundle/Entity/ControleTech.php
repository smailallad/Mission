<?php

namespace AppBundle\Entity;

/**
 * ControleTech
 */
class ControleTech
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateDebut;

    /**
     * @var \DateTime
     */
    private $dateFin;

    /**
     * @var int|null
     */
    private $dernier;

    /**
     * @var string|null
     */
    private $obs;

    /**
     * @var \AppBundle\Entity\Vehicule
     */
    private $vehicule;


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
     * @return ControleTech
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
     * @return ControleTech
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
     * @return ControleTech
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
     * @return ControleTech
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
     * @return ControleTech
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
