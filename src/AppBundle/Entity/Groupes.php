<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Symfony\Component\Security\Core\User\AdvancedUserInterface;
//use Symfony\Component\Validator\Constraints as Assert;


/**
 * Groupes
 *
 * @ORM\Table(name="groupes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupsRepository")
 * @UniqueEntity(fields={"groupname"},message="Valeur existe déjà sur la base de donnée.")
 */
class Groupes
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
     * @ORM\Column(type="array")
     *
     */
    private $roles;

   /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,unique=true)
     */
    private $groupname;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return Groupes
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set groupname
     *
     * @param string $groupname
     *
     * @return Groupes
     */
    public function setGroupname($groupname)
    {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * Get groupname
     *
     * @return string
     */
    public function getGroupname()
    {
        return $this->groupname;
    }

    public function __toString()
    {
        return (string) $this->getGroupname();
    }
}
