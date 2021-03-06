<?php

namespace AppBundle\Entity;

//use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Roles
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RolesRepository")
 * @UniqueEntity(fields={"rolename"},message="Valeur existe déjà sur la base de donnée.")
 */
class Roles
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
     * @ORM\Column(name="rolename", type="string", length=255, unique=true)
     */
    private $rolename;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rolename
     *
     * @param string $rolename
     *
     * @return Roles
     */
    public function setRolename($rolename)
    {
        $this->rolename = $rolename;

        return $this;
    }

    /**
     * Get rolename
     *
     * @return string
     */
    public function getRolename()
    {
        return $this->rolename;
    }

    public function __toString() {
        return $this->rolename;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Roles
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
