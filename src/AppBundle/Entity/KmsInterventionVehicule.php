<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\KmsInterventionVehiculeRepository")
 * @UniqueEntity(fields={"interventionVehicule","marque"},message="Marque ajouter déja.")
 */
class KmsInterventionVehicule
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Marque")
	 * @ORM\JoinColumn(nullable=false)
    */
    private $marque; 
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\InterventionVehicule")
	 * @ORM\JoinColumn(nullable=false)
    */
    private $interventionVehicule;
        
    /**
	 * @Assert\Range(
     *      min = 1000,
     *      max = 300000,
     *      minMessage = "Valeur Min : {{ limit }}",
     *      maxMessage = "Valeur Max : {{ limit }}"
     * )
     * @ORM\Column(type="integer")
    */
    private $kms;
		
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)		 
    */
    private $obs;


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return KmsInterventionVehicule
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set kms.
     *
     * @param int $kms
     *
     * @return KmsInterventionVehicule
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
     * @return KmsInterventionVehicule
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
     * @return KmsInterventionVehicule
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
     * @return KmsInterventionVehicule
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
