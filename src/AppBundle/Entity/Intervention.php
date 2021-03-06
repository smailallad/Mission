<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use AppBundle\Entity\InterventionUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Intervention
 *
 * @ORM\Table(name="intervention")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterventionRepository")
 * @UniqueEntity(fields={"dateIntervention","mission","site","prestation"},
 * errorPath ="site",
 * message="Valeur existe déjà sur la base de donnée.")
 *
 */
class Intervention
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
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\InterventionUser",mappedBy="intervention")
    */
    private $interventionUsers; 


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateReception", type="datetime")
     */
    private $dateReception; 

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateIntervention", type="datetime")
     */
    private $dateIntervention;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="text",nullable=true)
     */
    private $designation;
    /**
     * @var string
     *
     * @ORM\Column(type="text",nullable=true)
     */
    private $reserves;
    
    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mission")
     * @Assert\NotBlank(message="il faut affecté une mission")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mission;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site")
     * @Assert\NotBlank(message="il faut affecté un site")
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Prestation")
     * @Assert\NotBlank(message="il faut affecté une d'intervetion")
     */
    private $prestation;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vehicule")
     * @Assert\NotBlank(message="il faut affecté un véhicule")
     */
    private $vehicule;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Facture",inversedBy="interventions")
     * @JoinColumn(name="facture_id",referencedColumnName="id")
     */
    private $facture;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PrestationBc",inversedBy="interventions")
     * @JoinColumn(name="prestation_bc_id", referencedColumnName="id")
    */
    private $prestationBc;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->quantite = 1;
        $this->dateReception = new DateTime(date('Y-m-d'));
        $this->dateIntervention = new DateTime(date('Y-m-d'));
        $this->tarif=0;
        $this->interventionUsers = new \Doctrine\Common\Collections\ArrayCollection();
       
    }
    
    //public function addInterventionUser(\AppBundle\Entity\InterventionUser $interventionUser)
    //{
    //$this->interventionUsers[] = $interventionUser;
    //return $this;
    //}

    //public function removeInterventionUser(\AppBundle\Entity\InterventionUser $interventionUser)
    //{
    //$this->interventionUsers->removeElement($interventionUser);
    //}

    public function getInterventionUsers()
    {
    return $this->interventionUsers;
    }
    
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
     * Set dateReception
     *
     * @param \DateTime $dateReception
     *
     * @return Intervention
     */
    public function setDateReception($dateReception)
    {
        $this->dateReception = $dateReception;

        return $this;
    }

    /**
     * Get dateReception
     *
     * @return \DateTime
     */
    public function getDateReception()
    {
        return $this->dateReception;
    }

    /**
     * Set dateIntervention
     *
     * @param \DateTime $dateIntervention
     *
     * @return Intervention
     */
    public function setDateIntervention($dateIntervention)
    {
        $this->dateIntervention = $dateIntervention;

        return $this;
    }

    /**
     * Get dateIntervention
     *
     * @return \DateTime
     */
    public function getDateIntervention()
    {
        return $this->dateIntervention;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return Intervention
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return Intervention
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return integer
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return Intervention
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
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return Intervention
     */
    public function setSite(\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set prestation
     *
     * @param \AppBundle\Entity\Prestation $prestation
     *
     * @return Intervention
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
     * Set vehicule
     *
     * @param \AppBundle\Entity\Vehicule $vehicule
     *
     * @return Intervention
     */
    public function setVehicule(\AppBundle\Entity\Vehicule $vehicule = null)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return \AppBundle\Entity\Vehicule
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Intervention
     */
    public function setFacture(\AppBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \AppBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Intervention
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add interventionUser.
     *
     * @param \AppBundle\Entity\InterventionUser $interventionUser
     *
     * @return Intervention
     */
    public function addInterventionUser(\AppBundle\Entity\InterventionUser $interventionUser)
    {
        $this->interventionUsers[] = $interventionUser;

        return $this;
    }

    /**
     * Remove interventionUser.
     *
     * @param \AppBundle\Entity\InterventionUser $interventionUser
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeInterventionUser(\AppBundle\Entity\InterventionUser $interventionUser)
    {
        return $this->interventionUsers->removeElement($interventionUser);
    }

    /**
     * Set reserves.
     *
     * @param string|null $reserves
     *
     * @return Intervention
     */
    public function setReserves($reserves = null)
    {
        $this->reserves = $reserves;

        return $this;
    }

    /**
     * Get reserves.
     *
     * @return string|null
     */
    public function getReserves()
    {
        return $this->reserves;
    }


    /**
     * Set prestationBc.
     *
     * @param \AppBundle\Entity\PrestationBc|null $prestationBc
     *
     * @return Intervention
     */
    public function setPrestationBc(\AppBundle\Entity\PrestationBc $prestationBc = null)
    {
        $this->prestationBc = $prestationBc;

        return $this;
    }

    /**
     * Get prestationBc.
     *
     * @return \AppBundle\Entity\PrestationBc|null
     */
    public function getPrestationBc()
    {
        return $this->prestationBc;
    }
}
