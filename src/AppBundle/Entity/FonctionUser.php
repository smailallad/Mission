<?php
namespace AppBundle\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 *
 * @ORM\Table(name="fonction_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FonctionUserRepository")
 * @UniqueEntity(fields={"datefonction","user"},errorPath="datefonction",message="Une fonction attribuer déja à cette date.")
 */
class FonctionUser
{
    /**
     * @var int
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     * @JoinColumn(onDelete="CASCADE")
     */
    private $user;
    /**
     * @ORM\Column(type="string")
     */
    private $datefonction;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Fonction")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     * @JoinColumn(onDelete="CASCADE")
     */
    private $fonction;
    /**
     * Set datefonction
     *
     * @param date $datefonction
     *
     * @return FonctionUser
     */
    public function setDatefonction($datefonction)
    {
        $this->datefonction = $datefonction->format("Y-m-d");
        return $this;
    }
    /**
     * Get datefonction
     *
     * @return date
     */
    public function getDatefonction()
    {
        return new DateTime($this->datefonction);
    }
    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return FonctionUser
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;
        return $this;
    }
    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Set fonction
     *
     * @param \AppBundle\Entity\Fonction $fonction
     *
     * @return FonctionUser
     */
    public function setFonction(\AppBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;
        return $this;
    }
    /**
     * Get fonction
     *
     * @return \AppBundle\Entity\Fonction
     */
    public function getFonction()
    {
        return $this->fonction;
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
     * Set id.
     *
     * @param int $id
     *
     * @return FonctionUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
