<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="responsable_bc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResponsableBcRepository")
 * @UniqueEntity(fields={"nom"},message="Valeur existe déjà sur la base de donnée.")
 */
class ResponsableBc
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
     * @return ResponsableBc
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

    public function __toString()
    {
        return $this->nom;
    }


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return ResponsableBc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
