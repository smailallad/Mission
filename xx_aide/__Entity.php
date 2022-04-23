<?php
    /** Site.php
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client",inversedBy="sites")
     * @JoinColumn(name="client_id", referencedColumnName="id",nullable=false)
     * @Assert\NotNull(message = "Entrer une valeur.")
     * @Groups("site_json")
    */
    private $client; 


    /** Client.php
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Site",mappedBy="client")
     */
    private $sites; 

    // Validation 
    use Symfony\Component\Validator\Context\ExecutionContextInterface;
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $quantite = $this->getQuantite();
        if ( $quantite <= 0 ) 
        {
            $context->buildViolation('Quantité invalide, valeur doit etre supperieur à zéro .')
            ->atPath('quantite')
            ->addViolation();
        }
        
        $montant = $this->getMontant();
        if ( $montant <= 0 ) 
        {
            $context->buildViolation('Montant invalide, valeur doit etre supperieur à zéro .')
            ->atPath('montant')
            ->addViolation();
        }        
    }