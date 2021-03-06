<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Depense
 *
 * @ORM\Table(name="depense")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepenseRepository")
 */
class Depense
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
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @var boolean
     * @ORM\Column(name="nouveau",type="boolean")
     */
    private $nouveau;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FamilleDepense")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $familleDepense;
    
    /**
    * @Assert\Callback
    */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $nom = $this->getNom();
        if (in_array(strtoupper($nom),["GAS OIL","GAS-OIL","ESSENCE","ESSENCES","ESSANCE","ESSANCES","SANS PLAN","CARBURANT","CARBURANTS"])) 
        {
            $context->buildViolation('Nom invalide, valeur doit etre differente de : ' . $nom)
            ->atPath('nom')
            ->addViolation();
        }
        
             
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Depense
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set familleDepense
     *
     * @param \AppBundle\Entity\FamilleDepense $familleDepense
     *
     * @return Depense
     */
    public function setFamilleDepense(\AppBundle\Entity\FamilleDepense $familleDepense = null)
    {
        $this->familleDepense = $familleDepense;

        return $this;
    }

    /**
     * Get familleDepense
     *
     * @return \AppBundle\Entity\FamilleDepense
     */
    public function getFamilleDepense()
    {
        return $this->familleDepense;
    }

    /**
     * Set nouveau
     *
     * @param boolean $nouveau
     *
     * @return Depense
     */
    public function setNouveau($nouveau)
    {
        $this->nouveau = $nouveau;

        return $this;
    }

    /**
     * Get nouveau
     *
     * @return boolean
     */
    public function getNouveau()
    {
        return $this->nouveau;
    }
    public function __toString()
    {
        return $this->nom;
    }


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Depense
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
