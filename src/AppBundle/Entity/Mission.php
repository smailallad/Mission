<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;


/**
 * Mission
 *
 * @ORM\Table(name="mission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MissionRepository")
 *
 */
class Mission
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
     * @var \DateTime
     *
     * @ORM\Column(name="depart", type="datetime")
     */
    private $depart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="retour", type="datetime")
     */
    private $retour;

    /**
     * @var bool
     *
     * @ORM\Column(name="vEmploye", type="boolean")
     */
    private $vEmploye;

    /**
     * @var bool
     *
     * @ORM\Column(name="vRollout", type="boolean")
     */
    private $vRollout;

    /**
     * @var bool
     *
     * @ORM\Column(name="vComptabilite", type="boolean")
     */
    private $vComptabilite;

    /**
     * @var float
     *
     * @ORM\Column(name="avance", type="float")
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "Le montant doit être égal ou supperieur à {{ value }}"
     *      )
     */
    private $avance;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;

    public function __construct()
    {
        $this->vEmploye = false;
        $this->vRollout = false;
        $this->vComptabilite = false;
        $this->avance = 0;
    }

    /**
    * @Assert\Callback
    */
    public function verifierDate(ExecutionContext $context) 
    {
        if ($this->getDepart() > $this->getRetour())
        {
            $context->buildViolation('La date de retour doit être superieur ou égale à la date de départ.')
                    ->atPath('retour')
                    //->setParameter('{{ value }}', "dddddd")
                    ->addViolation();
        }

        //throw new \Exception('Message');
    
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
     * @return Mission
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
     * Set depart
     *
     * @param \DateTime $depart
     *
     * @return Mission
     */
    public function setDepart($depart)
    {
        $this->depart = $depart;

        return $this;
    }

    /**
     * Get depart
     *
     * @return \DateTime
     */
    public function getDepart()
    {
        return $this->depart;
    }

    /**
     * Set retour
     *
     * @param \DateTime $retour
     *
     * @return Mission
     */
    public function setRetour($retour)
    {
        $this->retour = $retour;

        return $this;
    }

    /**
     * Get retour
     *
     * @return \DateTime
     */
    public function getRetour()
    {
        return $this->retour;
    }

    /**
     * Set vEmploye
     *
     * @param boolean $vEmploye
     *
     * @return Mission
     */
    public function setVEmploye($vEmploye)
    {
        $this->vEmploye = $vEmploye;

        return $this;
    }

    /**
     * Get vEmploye
     *
     * @return bool
     */
    public function getVEmploye()
    {
        return $this->vEmploye;
    }

    /**
     * Set vRollout
     *
     * @param boolean $vRollout
     *
     * @return Mission
     */
    public function setVRollout($vRollout)
    {
        $this->vRollout = $vRollout;

        return $this;
    }

    /**
     * Get vRollout
     *
     * @return bool
     */
    public function getVRollout()
    {
        return $this->vRollout;
    }

    /**
     * Set vComptabilite
     *
     * @param boolean $vComptabilite
     *
     * @return Mission
     */
    public function setVComptabilite($vComptabilite)
    {
        $this->vComptabilite = $vComptabilite;

        return $this;
    }

    /**
     * Get vComptabilite
     *
     * @return bool
     */
    public function getVComptabilite()
    {
        return $this->vComptabilite;
    }

    /**
     * Set avance
     *
     * @param float $avance
     *
     * @return Mission
     */
    public function setAvance($avance)
    {
        $this->avance = $avance;

        return $this;
    }

    /**
     * Get avance
     *
     * @return float
     */
    public function getAvance()
    {
        return $this->avance;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Mission
     */
    public function setUser(\AppBundle\Entity\User $user = null)
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
     * Set id.
     *
     * @param int $id
     *
     * @return Mission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
