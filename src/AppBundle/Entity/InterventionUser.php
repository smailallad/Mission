<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Repository\InterventionUserRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InterventionUser
 *
 * @ORM\Table(name="intervention_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterventionUserRepository")
 *
 */
class InterventionUser 
{
    /**
     * @var int
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Intervention",inversedBy="interventionUsers")
     * @Assert\NotBlank(message="il faut affecté une intervention")
     * @ORM\JoinColumn(nullable=false)
     */
    private $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @Assert\NotBlank(message="il faut affecté un employe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


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
     * Set intervention.
     *
     * @param \AppBundle\Entity\Intervention $intervention
     *
     * @return InterventionUser
     */
    public function setIntervention(\AppBundle\Entity\Intervention $intervention)
    {
        $this->intervention = $intervention;

        return $this;
    }

    /**
     * Get intervention.
     *
     * @return \AppBundle\Entity\Intervention
     */
    public function getIntervention()
    {
        return $this->intervention;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return InterventionUser
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
     * @return InterventionUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
