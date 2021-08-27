<?php

namespace AppBundle\Entity;

/**
 * KmsIterventionVehicule
 */
class KmsIterventionVehicule
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $kms;

    /**
     * @var string|null
     */
    private $obs;

    /**
     * @var \AppBundle\Entity\Marque
     */
    private $marque;

    /**
     * @var \AppBundle\Entity\InterventionVehicule
     */
    private $interventionVehicule;


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
     * Set kms.
     *
     * @param int $kms
     *
     * @return KmsIterventionVehicule
     */
    public function setKms($kms)
    {
        $this->kms = $kms;

        return $this;
    }

    /**
     * Get kms.
     *
     * @return int
     */
    public function getKms()
    {
        return $this->kms;
    }

    /**
     * Set obs.
     *
     * @param string|null $obs
     *
     * @return KmsIterventionVehicule
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
     * Set marque.
     *
     * @param \AppBundle\Entity\Marque $marque
     *
     * @return KmsIterventionVehicule
     */
    public function setMarque(\AppBundle\Entity\Marque $marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return \AppBundle\Entity\Marque
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set interventionVehicule.
     *
     * @param \AppBundle\Entity\InterventionVehicule $interventionVehicule
     *
     * @return KmsIterventionVehicule
     */
    public function setInterventionVehicule(\AppBundle\Entity\InterventionVehicule $interventionVehicule)
    {
        $this->interventionVehicule = $interventionVehicule;

        return $this;
    }

    /**
     * Get interventionVehicule.
     *
     * @return \AppBundle\Entity\InterventionVehicule
     */
    public function getInterventionVehicule()
    {
        return $this->interventionVehicule;
    }
}
