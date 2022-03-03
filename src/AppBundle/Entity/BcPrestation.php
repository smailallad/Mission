<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BcPrestation
 *
 * @ORM\Table(name="bc_prestation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcPrestationRepository")
 * @UniqueEntity(fields={"prestation","zone"},
 * errorPath ="prestation",
 * message="Valeur existe déjà sur la base de donnée.")
 */
class BcPrestation
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
     *
     * @ORM\Column(name="qte", type="integer")
     */
    private $qte;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Prestation")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prestation;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Zone")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Bc")
     */
    private $bc;

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
     * @return BcPrestation
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
     * Set prestation
     *
     * @param \AppBundle\Entity\Prestation $prestation
     *
     * @return BcPrestation
     */
    public function setPrestation(\AppBundle\Entity\Prestation $prestation = null)
    {
        $this->prestation = $prestation;

        return $this;
    }

    /**
     * Get prestation
     *
     * @return \AppBundle\Entity\Prestation
     */
    public function getPrestation()
    {
        return $this->prestation;
    }

    /**
     * Set zone
     *
     * @param \AppBundle\Entity\Zone $zone
     *
     * @return BcPrestation
     */
    public function setZone(\AppBundle\Entity\Zone $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone
     *
     * @return \AppBundle\Entity\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return BcPrestation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set qte.
     *
     * @param int $qte
     *
     * @return BcPrestation
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
     * Set bc.
     *
     * @param \AppBundle\Entity\Bc|null $bc
     *
     * @return BcPrestation
     */
    public function setBc(\AppBundle\Entity\Bc $bc = null)
    {
        $this->bc = $bc;

        return $this;
    }

    /**
     * Get bc.
     *
     * @return \AppBundle\Entity\Bc|null
     */
    public function getBc()
    {
        return $this->bc;
    }
}
