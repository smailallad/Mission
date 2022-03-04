<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="bc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcRepository")
 * @UniqueEntity(fields={"num"},message="Valeur existe déjà sur la base de donnée.")
 */
class Bc
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
     * @ORM\Column(type="string", length=255,unique=true,nullable=false)
     */
    private $num;
    /**
     * @var date
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ResponsableBc")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
    */
    private $responsableBc; 
    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Po")
    * @Assert\NotNull(message = "Entrer une valeur.")
    * @ORM\JoinColumn(nullable=false)
    */
    private $po; 
    
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
     * Set num.
     *
     * @param string $num
     *
     * @return Bc
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num.
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Bc
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
     * Set responsableBc.
     *
     * @param \AppBundle\Entity\ResponsableBc $responsableBc
     *
     * @return Bc
     */
    public function setResponsableBc(\AppBundle\Entity\ResponsableBc $responsableBc)
    {
        $this->responsableBc = $responsableBc;

        return $this;
    }

    /**
     * Get responsableBc.
     *
     * @return \AppBundle\Entity\ResponsableBc
     */
    public function getResponsableBc()
    {
        return $this->responsableBc;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Bc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set po.
     *
     * @param \AppBundle\Entity\Po $po
     *
     * @return Bc
     */
    public function setPo(\AppBundle\Entity\Po $po)
    {
        $this->po = $po;

        return $this;
    }

    /**
     * Get po.
     *
     * @return \AppBundle\Entity\Po
     */
    public function getPo()
    {
        return $this->po;
    }
    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Bc
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
}
