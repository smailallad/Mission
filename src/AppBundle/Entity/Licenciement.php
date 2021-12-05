<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Licenciement
 *
 * @ORM\Table(name="licenciement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LicenciementRepository")
 */
class Licenciement
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
     * @var \DateTime
     *
     * @ORM\Column(name="licencier", type="datetime")
     */
    private $licencier;
    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255)
     */
    private $motif;
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Recrutement")
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recrutement;

    


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
     * Set licencier.
     *
     * @param \DateTime $licencier
     *
     * @return Licenciement
     */
    public function setLicencier($licencier)
    {
        $this->licencier = $licencier;

        return $this;
    }

    /**
     * Get licencier.
     *
     * @return \DateTime
     */
    public function getLicencier()
    {
        return $this->licencier;
    }

    /**
     * Set motif.
     *
     * @param string $motif
     *
     * @return Licenciement
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif.
     *
     * @return string
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set recrutement.
     *
     * @param \AppBundle\Entity\Recrutement $recrutement
     *
     * @return Licenciement
     */
    public function setRecrutement(\AppBundle\Entity\Recrutement $recrutement)
    {
        $this->recrutement = $recrutement;

        return $this;
    }

    /**
     * Get recrutement.
     *
     * @return \AppBundle\Entity\Recrutement
     */
    public function getRecrutement()
    {
        return $this->recrutement;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Licenciement
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
