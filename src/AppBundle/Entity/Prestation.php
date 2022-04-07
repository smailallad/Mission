<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Prestation
 *
 * @ORM\Table(name="prestation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationRepository")
 * @UniqueEntity(fields = {"projet","nom"},message ="Prestation saisie déja pour se projet")
 */
class Prestation
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Projet")
     * @Assert\NotNull(message = "Entrer une valeur du projet.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projet;

     /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PrestationBc",mappedBy="prestation")
     */
    private $prestationBcs;

    /**
     * Get id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Prestation
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }


    /**
     * Set projet.
     *
     * @param \AppBundle\Entity\Projet $projet
     *
     * @return Prestation
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
     * Set active.
     *
     * @param bool $active
     *
     * @return Prestation
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
    public function __toString()
    {
        return $this->nom;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prestationBcs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add prestationBc.
     *
     * @param \AppBundle\Entity\PrestationBc $prestationBc
     *
     * @return Prestation
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
}
