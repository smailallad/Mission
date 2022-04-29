<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
//use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert; 

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"email"},message="Valeur existe déjà sur la base de donnée.")
 * @UniqueEntity(fields={"username"},message="Valeur existe déjà sur la base de donnée.")
 * @UniqueEntity(fields={"nom"},message="Valeur existe déjà sur la base de donnée.")
 */
class User implements UserInterface
//class User implements AdvancedUserInterface

{ 
    

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255,unique=true)
     */
    private $username;
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $nom; 
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Mot de passe doit dépassé {{ limit }} caractéres.",
     * )
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $naissance;
    
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Groupes",cascade="persist")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $groupes;

    private $salt;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    private $active ;

     /**
     * @ORM\Column(name="mission", type="boolean")
     */
    private $mission ;

    
    public function __construct ()
    {
        $this-> active = false ;
        $this-> mission = false;
        //$this ->groupes = new \Doctrine\Common\Collections\ArrayCollection();
        //$this->groupes = 1;

    }
    
    // les methodes a ajouter manuellement

    /**
    * Returns the roles granted to the user.
    *
    * @return Role[] The user roles
    */
    public function getRoles()
    {
        return $this->groupes->getRoles();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }
    public function isActive()
    {
        return $this->active;

    }

    public function isEnabled()
    {
        return $this->active;

    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

     public function getSalt()
    {
        return $this->salt;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->username;
    }

    // serialize and unserialize must be updated - see below
    public function serialize()
    {
        return serialize(array(
            // ...
            $this->isActive,
        ));
    }
    public function unserialize($serialized)
    {
        list (
            // ...
            $this->isActive,
        ) = unserialize($serialized);
    }
    public function __toString()
    {
        return (string) $this->getUsername();
    }

    // Fin methodes a ajouter manuellement

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return User
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
     * Set email.
     *
     * @param string $email
     *
     * @return User
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
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set naissance.
     *
     * @param \DateTime $naissance
     *
     * @return User
     */
    public function setNaissance($naissance)
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * Get naissance.
     *
     * @return \DateTime
     */
    public function getNaissance()
    {
        return $this->naissance;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set mission.
     *
     * @param bool $mission
     *
     * @return User
     */
    public function setMission($mission)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission.
     *
     * @return bool
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set groupes.
     *
     * @param \AppBundle\Entity\Groupes $groupes
     *
     * @return User
     */
    public function setGroupes(\AppBundle\Entity\Groupes $groupes = null)
    {
        $this->groupes = $groupes;

        return $this;
    }

    /**
     * Get groupes.
     *
     * @return \AppBundle\Entity\Groupes
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    
}
