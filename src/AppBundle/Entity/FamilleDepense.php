<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FamilleDepense
 *
 * @ORM\Table(name="famille_depense")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FamilleDepenseRepository")
 */
class FamilleDepense
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
     * @return FamilleDepense
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
    public function __toString()
    {
        return $this->nom;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return FamilleDepense
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
