<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Pointage
 *
 * @ORM\Table(name="pointageUser")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PointageUserRepository")
 * @UniqueEntity(fields={"date","user"},errorPath="date",message="Pointage déja effectuer pour cette employé à a cette date.")
 */
class PointageUser
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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    */
    private $user;
    
    /**
     * @var date
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @ORM\Column(name="hTravail", type="smallint")
     * * @Assert\Range(
     *      min = 0,
     *      max = 8,
     *      minMessage = "Le montant doit être égal ou supperieur à 0 ",
     *      maxMessage = "Le montant doit être égal ou inferieur à 8 "
     *      )
     */
    private $hTravail;

    /**
     * @ORM\Column(name="hRoute", type="smallint")
     */
    private $hRoute;

    /**
     * @ORM\Column(name="hSup", type="smallint")
     */
    private $hSup;


    /**
     * @ORM\Column(name="obs", type="string", length=255,nullable=true)
     */
    private $obs;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pointage")
     */
    private $pointage;

    public function __construct(){
        $this->date= new DateTime(date('Y-m-d'));
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return PointageUser
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
     * Set hTravail.
     *
     * @param int $hTravail
     *
     * @return PointageUser
     */
    public function setHTravail($hTravail)
    {
        $this->hTravail = $hTravail;

        return $this;
    }

    /**
     * Get hTravail.
     *
     * @return int
     */
    public function getHTravail()
    {
        return $this->hTravail;
    }

    /**
     * Set hRoute.
     *
     * @param int $hRoute
     *
     * @return PointageUser
     */
    public function setHRoute($hRoute)
    {
        $this->hRoute = $hRoute;

        return $this;
    }

    /**
     * Get hRoute.
     *
     * @return int
     */
    public function getHRoute()
    {
        return $this->hRoute;
    }

    /**
     * Set hSup.
     *
     * @param int $hSup
     *
     * @return PointageUser
     */
    public function setHSup($hSup)
    {
        $this->hSup = $hSup;

        return $this;
    }

    /**
     * Get hSup.
     *
     * @return int
     */
    public function getHSup()
    {
        return $this->hSup;
    }

    /**
     * Set obs.
     *
     * @param string|null $obs
     *
     * @return PointageUser
     */
    public function setObs($obs = null)
    {
        $this->obs = $obs;

        return $this;
    }

    /**
     * Get obs.
     *
     * @return string|null
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * Set pointage.
     *
     * @param \AppBundle\Entity\Pointage|null $pointage
     *
     * @return PointageUser
     */
    public function setPointage(\AppBundle\Entity\Pointage $pointage = null)
    {
        $this->pointage = $pointage;

        return $this;
    }

    /**
     * Get pointage.
     *
     * @return \AppBundle\Entity\Pointage|null
     */
    public function getPointage()
    {
        return $this->pointage;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return PointageUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return PointageUser
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
