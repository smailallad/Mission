<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarqueRepository")
 * @UniqueEntity(fields="nom", message="La marque existe déjà avec ce nom.")
 */
class Marque
{
	/**
	 * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,unique=true,nullable=false)
     * @Assert\Length(min = "3",max = "50",minMessage = "Le nom doit faire au moins {{ limit }} caractères",maxMessage = "Le nom ne peut pas être plus long que {{ limit }} caractères")
     */
    private $nom;

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
     * Set nom
     *
     * @param string $nom
     * @return Marque
     */
    public function setNom($nom)
    {
        $this->nom = strtoupper($nom);
    
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
     * Set id.
     *
     * @param int $id
     *
     * @return Marque
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    public function __toString()
    {
        return $this->nom;
    }
}
