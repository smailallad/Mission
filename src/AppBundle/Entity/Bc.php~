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
     * @ORM\Column(name="num", type="string", length=255, unique=true)
     */
    private $num;
    /**
     * @var date
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BcResponsable")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
    */
    private $bcResponsable; 
  
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
     * Set bcResponsable.
     *
     * @param \AppBundle\Entity\BcResponsable $bcResponsable
     *
     * @return Bc
     */
    public function setBcResponsable(\AppBundle\Entity\BcResponsable $bcResponsable)
    {
        $this->bcResponsable = $bcResponsable;

        return $this;
    }

    /**
     * Get bcResponsable.
     *
     * @return \AppBundle\Entity\BcResponsable
     */
    public function getBcResponsable()
    {
        return $this->bcResponsable;
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
}
