<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FraisMission
 *
 * @ORM\Table(name="frais_mission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FraisMissionRepository")
 * @UniqueEntity(fields={"dateFm","user"},
 * errorPath ="dateFm",
 * message="Valeur existe déjà sur la base de donnée.")
 */
class FraisMission
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
     * @var date
     * @ORM\Column(name="dateFm", type="date")
     */
    private $dateFm;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="string", length=255,nullable=true)
     */
    private $obs;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mission")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mission;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
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
     * Set montant
     *
     * @param float $montant
     *
     * @return FraisMission
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
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return FraisMission
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return FraisMission
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set obs
     *
     * @param string $obs
     *
     * @return FraisMission
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
     * Set dateFm
     *
     * @param \DateTime $dateFm
     *
     * @return FraisMission
     */
    public function setDateFm($dateFm)
    {
        $this->dateFm = $dateFm;

        return $this;
    }

    /**
     * Get dateFm
     *
     * @return \DateTime
     */
    public function getDateFm()
    {
        return $this->dateFm;
    }


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return FraisMission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
