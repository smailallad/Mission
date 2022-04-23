<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="bc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcRepository")
 * @UniqueEntity(fields={"num"},message="Valeur existe déjà sur la base de donnée.")
 */
class Bc
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
     * @var string
     *
     * @ORM\Column(type="string", length=255,unique=true,nullable=false)
     */
    private $num;
    /**
     * @var date
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active;
    /**
     * @ORM\Column(name="otp",type="string", length=255)
     */
    private $otp;
    /**
     * @ORM\Column(name="description",type="string", length=255)
     */
    private $description;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ResponsableBc")
     * @Assert\NotNull(message = "Entrer une valeur du responsable.")
     * @ORM\JoinColumn(nullable=false)
    */
    private $responsableBc; 
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Po")
    */
    private $po; 
    
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Projet")
    * @Assert\NotNull(message = "Entrer une valeur du projet.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $projet;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PrestationBc",mappedBy="bc")
     */
    private $prestationBcs;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facture",mappedBy="bc") 
     */
    private $factures;
    

    public function __toString()
    {
        return $this->num;
    }
    
    public function __construct()
    {
        $this->active = true;
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
     * Set num.
     *
     * @param string $num
     *
     * @return Bc
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num.
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Bc
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
     * Set responsableBc.
     *
     * @param \AppBundle\Entity\ResponsableBc $responsableBc
     *
     * @return Bc
     */
    public function setResponsableBc(\AppBundle\Entity\ResponsableBc $responsableBc)
    {
        $this->responsableBc = $responsableBc;

        return $this;
    }

    /**
     * Get responsableBc.
     *
     * @return \AppBundle\Entity\ResponsableBc
     */
    public function getResponsableBc()
    {
        return $this->responsableBc;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Bc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set po.
     *
     * @param \AppBundle\Entity\Po $po
     *
     * @return Bc
     */
    public function setPo(\AppBundle\Entity\Po $po)
    {
        $this->po = $po;

        return $this;
    }

    /**
     * Get po.
     *
     * @return \AppBundle\Entity\Po
     */
    public function getPo()
    {
        return $this->po;
    }
    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Bc
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Bc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set projet.
     *
     * @param \AppBundle\Entity\Projet $projet
     *
     * @return Bc
     */
    public function setProjet(\AppBundle\Entity\Projet $projet)
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * Get projet.
     *
     * @return \AppBundle\Entity\Projet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Set otp.
     *
     * @param string $otp
     *
     * @return Bc
     */
    public function setOtp($otp)
    {
        $this->otp = $otp;

        return $this;
    }

    /**
     * Get otp.
     *
     * @return string
     */
    public function getOtp()
    {
        return $this->otp;
    }

    /**
     * Add prestationBc.
     *
     * @param \AppBundle\Entity\PrestationBc $prestationBc
     *
     * @return Bc
     */
    public function addPrestationBc(\AppBundle\Entity\PrestationBc $prestationBc)
    {
        $this->prestationBcs[] = $prestationBc;

        return $this;
    }

    /**
     * Remove prestationBc.
     *
     * @param \AppBundle\Entity\PrestationBc $prestationBc
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePrestationBc(\AppBundle\Entity\PrestationBc $prestationBc)
    {
        return $this->prestationBcs->removeElement($prestationBc);
    }

    /**
     * Get prestationBcs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrestationBcs()
    {
        return $this->prestationBcs;
    }

    /**
     * Add facture.
     *
     * @param \AppBundle\Entity\facture $facture
     *
     * @return Bc
     */
    public function addFacture(\AppBundle\Entity\facture $facture)
    {
        $this->factures[] = $facture;

        return $this;
    }

    /**
     * Remove facture.
     *
     * @param \AppBundle\Entity\facture $facture
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFacture(\AppBundle\Entity\facture $facture)
    {
        return $this->factures->removeElement($facture);
    }

    /**
     * Get factures.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFactures()
    {
        return $this->factures;
    }
}
