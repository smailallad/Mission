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
 * @UniqueEntity(fields = {"nom"},message ="Le nom saisie déja pour se sous projet")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SousProjet")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sousProjet;

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
     * Set sousProjet
     *
     * @param \AppBundle\Entity\SousProjet $sousProjet
     *
     * @return Prestation
     */
    public function setSousProjet(\AppBundle\Entity\SousProjet $sousProjet = null)
    {
        $this->sousProjet = $sousProjet;

        return $this;
    }

    /**
     * Get sousProjet
     *
     * @return \AppBundle\Entity\SousProjet
     */
    public function getSousProjet()
    {
        return $this->sousProjet;
    }
        public function __toString()
    {
        return $this->nom;
    }

}
