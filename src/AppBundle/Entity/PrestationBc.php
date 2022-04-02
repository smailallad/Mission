<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="prestation_bc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationBcRepository")
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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site")
    */
    private $site; 
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Bc",inversedBy="prestationBcs")
    * @JoinColumn(name="bc_id", referencedColumnName="id",nullable=false)
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @Groups("site_json")
    */
    private $bc;
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
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $unite;
    


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
     * Set quantite.
     *
     * @param int $quantite
     *
     * @return PrestationBc
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite.
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
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

    /**
     * Set site.
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return PrestationBc
     */
    public function setSite(\AppBundle\Entity\Site $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site.
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
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
     * Set unite.
     *
     * @param string $unite
     *
     * @return PrestationBc
     */
    public function setUnite($unite)
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite.
     *
     * @return string
     */
    public function getUnite()
    {
        return $this->unite;
    }
}
