<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Wilaya
 *
 * @ORM\Table(name="wilaya")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WilayaRepository")
 */
class Wilaya
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"site_json"})  
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     * @Groups({"site_json"})  
     */
    private $nom;
   /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Zone")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $zone;
    /**
     * @var float
     *
     * @ORM\Column(name="montantFm", type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "Le montant doit être égal ou supperieur à 0"
     *      )
     */
    private $montantFm;
    
    public function __toString()
    {
        return $this->nom;
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
     * Set nom.
     *
     * @param string $nom
     *
     * @return Wilaya
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set montantFm.
     *
     * @param float $montantFm
     *
     * @return Wilaya
     */
    public function setMontantFm($montantFm)
    {
        $this->montantFm = $montantFm;

        return $this;
    }

    /**
     * Get montantFm.
     *
     * @return float
     */
    public function getMontantFm()
    {
        return $this->montantFm;
    }

    /**
     * Set zone.
     *
     * @param \AppBundle\Entity\Zone $zone
     *
     * @return Wilaya
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
}
