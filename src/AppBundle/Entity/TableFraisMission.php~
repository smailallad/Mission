<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TableFraisMission
 *
 * @ORM\Table(name="table_frais_mission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TableFraisMissionRepository")
 */
class TableFraisMission
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
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Wilaya")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wilaya;

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
     * @return TableFraisMission
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
     * Set wilaya
     *
     * @param \AppBundle\Entity\Wilaya $wilaya
     *
     * @return TableFraisMission
     */
    public function setWilaya(\AppBundle\Entity\Wilaya $wilaya = null)
    {
        $this->wilaya = $wilaya;

        return $this;
    }

    /**
     * Get wilaya
     *
     * @return \AppBundle\Entity\Wilaya
     */
    public function getWilaya()
    {
        return $this->wilaya;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return TableFraisMission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
