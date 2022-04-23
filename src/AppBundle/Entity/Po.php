<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * po
 *
 * @ORM\Table(name="po")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PoRepository")
 */
class Po
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
     * @ORM\Column(type="string", length=255,unique=true,nullable=false)
     */
    private $num;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active;
   
    public function __construct()
    {
        $this->active = true;
    }
    public function __toString()
    {
        return $this->num;
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
     * @return po
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
     * Set num.
     *
     * @param string $num
     *
     * @return Po
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
     * Set active.
     *
     * @param bool $active
     *
     * @return Po
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
     * Set id.
     *
     * @param int $id
     *
     * @return Po
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
