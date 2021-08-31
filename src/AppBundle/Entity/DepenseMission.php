<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Depense
 *
 * @ORM\Table(name="depense_mission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepenseMissionRepository")
 */
class DepenseMission
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Depense")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $depense;
 
    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mission")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mission; 

    /**
     * @var float
     * @ORM\Column(name="montant", type="float")
     * 
     */
    private $montant;

    /**
     * @var date
     * @ORM\Column(name="dateDep", type="date")
     */
    private $dateDep;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JustificationDepense")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
    */
    private $justificationDepense;

    /**
     * @var string
     * @ORM\Column(name="obs",type="string", length=255, nullable=true)
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
     * @return DepenseMission
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
     * Set obs
     *
     * @param string $obs
     *
     * @return DepenseMission
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
     * Set depense
     *
     * @param \AppBundle\Entity\Depense $depense
     *
     * @return DepenseMission
     */
    public function setDepense(\AppBundle\Entity\Depense $depense = null)
    {
        $this->depense = $depense;

        return $this;
    }

    /**
     * Get depense
     *
     * @return \AppBundle\Entity\Depense
     */
    public function getDepense()
    {
        return $this->depense;
    }

    /**
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return DepenseMission
     */
    public function setMission(\AppBundle\Entity\Mission $mission = null)
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
     * Set justificationDepense
     *
     * @param \AppBundle\Entity\JustificationDepense $justificationDepense
     *
     * @return DepenseMission
     */
    public function setJustificationDepense(\AppBundle\Entity\JustificationDepense $justificationDepense = null)
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
     * Set dateDep
     *
     * @param \DateTime $dateDep
     *
     * @return DepenseMission
     */
    public function setDateDep($dateDep)
    {
        $this->dateDep = $dateDep;

        return $this;
    }

    /**
     * Get dateDep
     *
     * @return \DateTime
     */
    public function getDateDep()
    {
        return $this->dateDep;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return DepenseMission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
