<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="prestation_bc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResponsableBcRepository")
 * @UniqueEntity(fields={"nom"},message="Valeur existe déjà sur la base de donnée.")
 */
class PrestationBc
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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Bc")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $bc;
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Zone")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $zone;
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Prestation")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $prestation;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $qte;

    

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
     * Set montant.
     *
     * @param float $montant
     *
     * @return PrestationBc
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant.
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set qte.
     *
     * @param int $qte
     *
     * @return PrestationBc
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
     * @param \AppBundle\Entity\Bc $bc
     *
     * @return PrestationBc
     */
    public function setBc(\AppBundle\Entity\Bc $bc)
    {
        $this->bc = $bc;

        return $this;
    }

    /**
     * Get bc.
     *
     * @return \AppBundle\Entity\Bc
     */
    public function getBc()
    {
        return $this->bc;
    }

    /**
     * Set zone.
     *
     * @param \AppBundle\Entity\Zone $zone
     *
     * @return PrestationBc
     */
    public function setZone(\AppBundle\Entity\Zone $zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone.
     *
     * @return \AppBundle\Entity\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set prestation.
     *
     * @param \AppBundle\Entity\Prestation $prestation
     *
     * @return PrestationBc
     */
    public function setPrestation(\AppBundle\Entity\Prestation $prestation)
    {
        $this->prestation = $prestation;

        return $this;
    }

    /**
     * Get prestation.
     *
     * @return \AppBundle\Entity\Prestation
     */
    public function getPrestation()
    {
        return $this->prestation;
    }
}
