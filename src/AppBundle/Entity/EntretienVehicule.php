<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *EntretienVehicule
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="idxUnique", columns={"date", "vehicule_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntretienVehiculeRepository")
 * @UniqueEntity(fields={"date","vehicule"}, message="Entretien de ce vehicule existe déjà avec cette date.")
 */
class EntretienVehicule
{
     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vehicule",cascade="persist")
		*/
    private $vehicule;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",cascade="persist")
    */
	private $user;

    /**
 	 * @Assert\Date(message="Date incorrecte.")
     * @ORM\Column(name="date", type="date",nullable=false)
     */
    private $date;

    /**
	 * @Assert\Regex(pattern="/[\d]+/",message="Kilometrage incorrecte ." )
  	 * @Assert\Range(
     *      min = 1,
     *      minMessage = "Kilometrage incorrecte."
     * )
		 * @ORM\Column(name="kms", type="integer",nullable=false)
     */
    private $kms;

    /**
	   * @ORM\Column(name="obs", type="string", length=255, nullable=true)
     */
    private $obs;

    public function __construct()
    {
      $this->date = new \Datetime();
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return EntretienVehicule
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set kms.
     *
     * @param int $kms
     *
     * @return EntretienVehicule
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
     * @return EntretienVehicule
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
     * @return EntretienVehicule
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

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return EntretienVehicule
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return EntretienVehicule
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
