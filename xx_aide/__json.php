<?php
    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=255)
     * @Groups({"site_json"})     
     */
    private $nom;

public function getSiteClientZone($client,$zone,SerializerInterface $serializer)
    {
        $pageNum= 1;
        $manager = $this->getDoctrine()->getManager();
        $sites = $manager->getRepository("AppBundle:Site")->getSiteClientZone($client,$zone);
        $sites = $sites->getQuery()->getResult();
        //dump($sites);

        $sites = $serializer->serialize(
            $sites,
            'json',
            ['groups' => ['site_json']]
        );
        
        return $this->json(["sites"     => $sites,
                            "pageNum"   => $pageNum,
                            ],
                            200);
      
    }

 function getSite(event) {
    var zone = $('#prestation_bc_zone').val();
    var client = $('#client').html();
    var cURL = Routing.generate('bc_get_site_client_zone', { client:client,zone: zone });
    
    $('#prestation_bc_site option'). remove();
    $.post(
            cURL,
            function (data) 
            {   //console.log(data);
                var pageNum = data.pageNum;
                var sites = JSON.parse(data.sites);
                //console.log(data.interventions.length);
                //console.log(sites);
                $('#prestation_bc_site').append($('<option></option>').val('').html(''));
                for (i = 0; i < sites.length; i++) {
                    var id = sites[i]['id'];
                    var nom = sites[i]['nom'];
                    //console.log(nom);
                    $('#prestation_bc_site').append($('<option></option>').val(id).html(nom));
                }
            }
        )

    }
