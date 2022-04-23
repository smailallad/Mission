<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Client
 *
 * @ORM\Table(name="client_facturation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class ClientFacturation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"site"})     
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $rc;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $tin;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $nif;

    public function __tostring()
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
     * Set adresse.
     *
     * @param string $adresse
     *
     * @return ClientFacturation
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse.
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set telephone.
     *
     * @param string $telephone
     *
     * @return ClientFacturation
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone.
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set rc.
     *
     * @param string $rc
     *
     * @return ClientFacturation
     */
    public function setRc($rc)
    {
        $this->rc = $rc;

        return $this;
    }

    /**
     * Get rc.
     *
     * @return string
     */
    public function getRc()
    {
        return $this->rc;
    }

    /**
     * Set tin.
     *
     * @param string $tin
     *
     * @return ClientFacturation
     */
    public function setTin($tin)
    {
        $this->tin = $tin;

        return $this;
    }

    /**
     * Get tin.
     *
     * @return string
     */
    public function getTin()
    {
        return $this->tin;
    }

    /**
     * Set nif.
     *
     * @param string $nif
     *
     * @return ClientFacturation
     */
    public function setNif($nif)
    {
        $this->nif = $nif;

        return $this;
    }

    /**
     * Get nif.
     *
     * @return string
     */
    public function getNif()
    {
        return $this->nif;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return ClientFacturation
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
     * Set id.
     *
     * @param int $id
     *
     * @return ClientFacturation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
