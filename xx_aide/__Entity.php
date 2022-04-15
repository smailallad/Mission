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
