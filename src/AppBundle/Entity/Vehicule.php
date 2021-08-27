<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vehicule
 *
 * @ORM\Table(name="vehicule")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VehiculeRepository")
 */
class Vehicule
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
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @var string
     * @Assert\Regex(
     * pattern="/[\d]+[\-][0-9]{3}[\-][0-9]{2}/",
     * message="Matricule incorrecte au format 00000-000-00."
     * )
     * @ORM\Column(type="string", length=15,unique=true,nullable=false)
     */
    private $matricule;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Marque")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=true)
    */
	private $marque;
    /**
	* @Assert\Range(
    *      min = 7,
	*	   max =90,
    *      minMessage = "Valeur Minimal : {{ limit }} jours",
	*	   maxMessage = "Valeur Maximal : {{ limit }} jours",)
	* @ORM\Column(type="integer",nullable=true)
    */
    private $nbrjAlertRelever;

	/**
	* @Assert\Range(
    *      min = 1,
    *      minMessage = "Valeur Minimal : {{ limit }}")
	* @ORM\Column(type="integer",nullable=true)
    */
    private $kmsRelever;

	/**
 	 * @Assert\Date(message="Date incorrecte.")
     * @ORM\Column(type="date",nullable=true)
     */
    private $dateRelever;

    public function __construct()
    {
        $this->dateRelever = new \Datetime();
	    $this->kmsRelever=0;
    }

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Vehicule
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
     * Set matricule
     *
     * @param string $matricule
     *
     * @return Vehicule
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Vehicule
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
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
     * Set id.
     *
     * @param int $id
     *
     * @return Vehicule
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set nbrjAlertRelever.
     *
     * @param int|null $nbrjAlertRelever
     *
     * @return Vehicule
     */
    public function setNbrjAlertRelever($nbrjAlertRelever = null)
    {
        $this->nbrjAlertRelever = $nbrjAlertRelever;

        return $this;
    }

    /**
     * Get nbrjAlertRelever.
     *
     * @return int|null
     */
    public function getNbrjAlertRelever()
    {
        return $this->nbrjAlertRelever;
    }

    /**
     * Set kmsRelever.
     *
     * @param int|null $kmsRelever
     *
     * @return Vehicule
     */
    public function setKmsRelever($kmsRelever = null)
    {
        $this->kmsRelever = $kmsRelever;

        return $this;
    }

    /**
     * Get kmsRelever.
     *
     * @return int|null
     */
    public function getKmsRelever()
    {
        return $this->kmsRelever;
    }

    /**
     * Set dateRelever.
     *
     * @param \DateTime|null $dateRelever
     *
     * @return Vehicule
     */
    public function setDateRelever($dateRelever = null)
    {
        $this->dateRelever = $dateRelever;

        return $this;
    }

    /**
     * Get dateRelever.
     *
     * @return \DateTime|null
     */
    public function getDateRelever()
    {
        return $this->dateRelever;
    }

    /**
     * Set marque.
     *
     * @param \AppBundle\Entity\Marque|null $marque
     *
     * @return Vehicule
     */
    public function setMarque(\AppBundle\Entity\Marque $marque = null)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return \AppBundle\Entity\Marque|null
     */
    public function getMarque()
    {
        return $this->marque;
    }
}
