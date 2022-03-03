<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="bc_responsable")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcResponsableRepository")
 * @UniqueEntity(fields={"num"},message="Valeur existe déjà sur la base de donnée.")
 */
class BcResponsable
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
     * @ORM\Column(name="num", type="string", length=255, unique=true)
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
     * @return BcResponsable
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
     * @return BcResponsable
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
