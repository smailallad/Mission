<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="ets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Ets
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
     * @ORM\Column(type="string",length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $rc;

     /**
     * @ORM\Column(type="string",length=255)
     */
    private $art;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $nif;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $nis;

    /**
     * @ORM\Column(type="float")
     */
    private $capital;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $email;

        /**
     * @ORM\Column(type="string",length=255)
     */
    private $rib;


   

    
    


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
     * @return Ets
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
     * Set adresse.
     *
     * @param string $adresse
     *
     * @return Ets
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
     * Set rc.
     *
     * @param string $rc
     *
     * @return Ets
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
     * Set art.
     *
     * @param string $art
     *
     * @return Ets
     */
    public function setArt($art)
    {
        $this->art = $art;

        return $this;
    }

    /**
     * Get art.
     *
     * @return string
     */
    public function getArt()
    {
        return $this->art;
    }

    /**
     * Set nif.
     *
     * @param string $nif
     *
     * @return Ets
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
     * Set nis.
     *
     * @param string $nis
     *
     * @return Ets
     */
    public function setNis($nis)
    {
        $this->nis = $nis;

        return $this;
    }

    /**
     * Get nis.
     *
     * @return string
     */
    public function getNis()
    {
        return $this->nis;
    }

    /**
     * Set capital.
     *
     * @param float $capital
     *
     * @return Ets
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * Get capital.
     *
     * @return float
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * Set telephone.
     *
     * @param string $telephone
     *
     * @return Ets
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
     * Set mobile.
     *
     * @param string $mobile
     *
     * @return Ets
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile.
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Ets
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set rib.
     *
     * @param string $rib
     *
     * @return Ets
     */
    public function setRib($rib)
    {
        $this->rib = $rib;

        return $this;
    }

    /**
     * Get rib.
     *
     * @return string
     */
    public function getRib()
    {
        return $this->rib;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Ets
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
