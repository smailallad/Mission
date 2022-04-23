<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Projet
 *
 * @ORM\Table(name="projet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjetRepository")
 * @UniqueEntity(fields = {"nom"},message ="Projet saisie déja.")
 */
class Projet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("projet_json")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     * @Groups("projet_json")
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClientFacturation")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clientFacturation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date;

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
     * @return Projet
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
    public function __tostring()
    {
        return $this->nom;
    }


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Projet
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set client.
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Projet
     */
    public function setClient(\AppBundle\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Projet
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

   



    /**
     * Set clientFacturation.
     *
     * @param \AppBundle\Entity\ClientFacturation $clientFacturation
     *
     * @return Projet
     */
    public function setClientFacturation(\AppBundle\Entity\ClientFacturation $clientFacturation)
    {
        $this->clientFacturation = $clientFacturation;

        return $this;
    }

    /**
     * Get clientFacturation.
     *
     * @return \AppBundle\Entity\ClientFacturation
     */
    public function getClientFacturation()
    {
        return $this->clientFacturation;
    }
}
