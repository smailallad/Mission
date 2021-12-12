<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Recrutement
 *
 * @ORM\Table(name="recrutement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecrutementRepository")
 * @UniqueEntity(fields={"user","date"},message="Valeur existe déjà sur la base de donnée.")
 */
class Recrutement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
     /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable = false)
     * @Assert\NotNull(message = "Entrer une valeur.")
     */
    private $user;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recruter", type="datetime")
     */
    private $recruter;

    

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
     * Set recruter.
     *
     * @param \DateTime $recruter
     *
     * @return Recrutement
     */
    public function setRecruter($recruter)
    {
        $this->recruter = $recruter;

        return $this;
    }

    /**
     * Get recruter.
     *
     * @return \DateTime
     */
    public function getRecruter()
    {
        return $this->recruter;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Recrutement
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Recrutement
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
