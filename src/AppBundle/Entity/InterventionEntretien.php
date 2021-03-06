<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterventionEntretienRepository")
 * @UniqueEntity(fields={"interventionVehicule","entretienVehicule"},message="Intervention saisie déja.")
 */
class InterventionEntretien
{
	/**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EntretienVehicule")
	 * @ORM\JoinColumn(nullable=false)
    */
    private $entretienVehicule;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\InterventionVehicule")
	 * @ORM\JoinColumn(nullable=false)
    */
    private $interventionVehicule; 
        
    /**
     * @var integer
		 * @Assert\Range(
     *      min = 1,
     *      max = 100,
     *      minMessage = "Valeur Min : {{ limit }}",
     *      maxMessage = "Valeur Max : {{ limit }}"
     * )
     * @ORM\Column(name="qte", type="integer")
     */
    private $qte;
		
		/**
		 * @var string $obs
		 * @ORM\Column(name="obs", type="string", length=255, nullable=true)		 
     */
    private $obs;

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
     * Set qte.
     *
     * @param int $qte
     *
     * @return InterventionEntretien
     */
    public function setQte($qte)
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * Get qte.
     *
     * @return int
     */
    public function getQte()
    {
        return $this->qte;
    }

    /**
     * Set obs.
     *
     * @param string|null $obs
     *
     * @return InterventionEntretien
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
     * Set entretienVehicule.
     *
     * @param \AppBundle\Entity\EntretienVehicule $entretienVehicule
     *
     * @return InterventionEntretien
     */
    public function setEntretienVehicule(\AppBundle\Entity\EntretienVehicule $entretienVehicule)
    {
        $this->entretienVehicule = $entretienVehicule;

        return $this;
    }

    /**
     * Get entretienVehicule.
     *
     * @return \AppBundle\Entity\EntretienVehicule
     */
    public function getEntretienVehicule()
    {
        return $this->entretienVehicule;
    }

    /**
     * Set interventionVehicule.
     *
     * @param \AppBundle\Entity\InterventionVehicule $interventionVehicule
     *
     * @return InterventionEntretien
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


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return InterventionEntretien
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
