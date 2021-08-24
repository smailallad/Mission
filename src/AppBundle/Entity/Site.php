<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SiteRepository")
 * @UniqueEntity(fields={"code"},message="Valeur existe déjà sur la base de donnée.")
 */
class Site
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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Wilaya")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
    */
    private $wilaya; 
    /**
     * @var bool
     *
     * @ORM\Column(name="nouveau", type="boolean")
     */
    private $nouveau;
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client",inversedBy="sites")
    * @JoinColumn(name="client_id", referencedColumnName="id",nullable=false)
     * @Assert\NotNull(message = "Entrer une valeur.")
    */
    private $client;

    public function __construct()
    {
        $this->nouveau = true;
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
     * Set code
     *
     * @param string $code
     *
     * @return Site
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Site
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
     * Set nouveau
     *
     * @param boolean $nouveau
     *
     * @return Site
     */
    public function setNouveau($nouveau)
    {
        $this->nouveau = $nouveau;
        return $this;
    }
    /**
     * Get nouveau
     *
     * @return bool
     */
    public function getNouveau()
    {
        return $this->nouveau;
    }
    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Site
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;
        return $this;
    }
    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
    /**
     * Set wilaya
     *
     * @param \AppBundle\Entity\Wilaya $wilaya
     *
     * @return Site
     */
    public function setWilaya(\AppBundle\Entity\Wilaya $wilaya = null)
    {
        $this->wilaya = $wilaya;
        return $this;
    }
    /**
     * Get wilaya
     *
     * @return \AppBundle\Entity\Wilaya
     */
    public function getWilaya()
    {
        return $this->wilaya;
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
     * @return Site
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
