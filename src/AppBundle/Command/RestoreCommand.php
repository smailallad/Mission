<?php
namespace AppBundle\Command;
use DateTime;
use AppBundle\Entity\Site;
use AppBundle\Entity\User;
use AppBundle\Entity\Zone;
use AppBundle\Entity\Client;
use AppBundle\Entity\Projet;
use AppBundle\Entity\Wilaya;
use AppBundle\Entity\Depense;
use AppBundle\Entity\Groupes;
use AppBundle\Entity\Mission;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Vehicule;
use AppBundle\Entity\Carburant;
use AppBundle\Entity\Prestation;
use AppBundle\Entity\SousProjet;
use AppBundle\Entity\Recrutement;
use AppBundle\Entity\FonctionUser;
use AppBundle\Entity\FraisMission;
use AppBundle\Entity\Intervention;
use AppBundle\Entity\DepenseMission;
use AppBundle\Entity\FamilleDepense;
use AppBundle\Entity\TarifPrestation;
use AppBundle\Entity\CarburantMission;
use AppBundle\Entity\InterventionUser;
use AppBundle\Entity\JustificationDepense;
use AppBundle\Entity\Pointage;
use AppBundle\Entity\PointageUser;
use AppBundle\Entity\Roles;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Security\Core\Role\Role;

class RestoreCommand extends ContainerAwareCommand
{  // php bin/console restore
    
    protected function configure()
    {
        $this
            ->setName('restore')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // green text
        //$output->writeln('<info>info</info>');
        // yellow text
        //$output->writeln('<comment>comment</comment>');
        // black text on a cyan background
        //$output->writeln('<question>question</question>');
        // white text on a red background
        //$output->writeln('<error>error</error>');


        $argument = $input->getArgument('argument');
        $output->writeln($argument);
        if ($input->getOption('option')) {
            // ...
        }
        $dbs = new \PDO("mysql:host=localhost;dbname=dbrtie;charset=utf8", "smil", "ads2160396");
        $dbd = new \PDO("mysql:host=localhost;dbname=rtie3.4;charset=utf8", "smil", "ads2160396");
        $manager = $this->getContainer()->get('doctrine')->getManager();
        $repository = $this->getContainer()->get('doctrine');
        $encoder = $this->getContainer()->get('security.password_encoder');

        /*
        Enlever Cle ID, ajout Method SetId()
        Groupes
        Client
        Zone
        User
        Prestation
        Intervention

        Ajouter une contrainte entre la relation Prestation et Sous Projet
        Table_Depense Griffe en double, supprimer le ID 87.
        
        */
        


        //********************************* */
        //          Début Traitement
        //********************************* */

        $output->writeln('');
        $output->writeln('********************************');
        $output->writeln('****** Début de Traitement *****');
        $output->writeln('********************************');
        $output->writeln('');

        //$output->writeln('<comment>===>  Table "Sous Projet" Modifie le code "0" a une autre valeur et change dans la table "Prestation" le "code_sous_projet" a la nouvelle valeur </comment>');
        //$output->writeln('<comment>===>  Supprimer les prestation "142" , "143" , "144" , "145" </comment>');
        //$output->writeln('<comment>===>  Créeer "Contraintes de clé étrangère" entre "prestation" et "sous_projet"</comment>');
        //$output->writeln('<comment>===>  Executer la commande : php bin/console restore <comment>');
        

              
        $i=1;
  
        if (1==2) 
        {
            // Roles ***************************************************************************************************************************
            $output->writeln('<question>' . $i . ' : Roles : </question>');

            $roles = new Roles();
            $roles
                    ->setId(1) 
                    ->setRolename('ROLE_ADMIN')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(2) 
                    ->setRolename('ROLE_GERANT')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(3) 
                    ->setRolename('ROLE_SUPER_COMPTABLE')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(4) 
                    ->setRolename('ROLE_COMPTABLE')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(5) 
                    ->setRolename('ROLE_ADMINISTRATION')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(6) 
                    ->setRolename('ROLE_ROLLOUT')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(7) 
                    ->setRolename('ROLE_BUREAU')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            $roles = new Roles();
            $roles 
                    ->setId(8) 
                    ->setRolename('ROLE_USER')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');

            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            
            
            $i++;
            // Groupes ***************************************************************************************************************************
            $output->writeln('<question>' . $i . ' : Groupe : </question>');

            $groupes = new Groupes();
            $groupes
                    ->setId(1) 
                    ->setGroupname('ADMIN')
                    ->setRoles(['ROLE_ADMIN'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(2) 
                    ->setGroupname('GERANT')
                    ->setRoles(['ROLE_GERANT'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(3) 
                    ->setGroupname('SUPER_COMPTABLE')
                    ->setRoles(['ROLE_SUPER_COMPTABLE'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(4) 
                    ->setGroupname('COMPTABLE')
                    ->setRoles(['ROLE_COMPTABLE'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(5) 
                    ->setGroupname('ADMINISTRATION')
                    ->setRoles(['ROLE_ADMINISTRATION'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(6) 
                    ->setGroupname('ROLLOUT')
                    ->setRoles(['ROLE_ROLLOUT'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(7) 
                    ->setGroupname('BUREAU')
                    ->setRoles(['ROLE_BUREAU'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            $groupes = new Groupes();
            $groupes 
                    ->setId(8) 
                    ->setGroupname('USER')
                    ->setRoles(['ROLE_USER'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');

            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            // Client ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Client : </question>');
            $client = new Client();
            $client 
                    ->setId(1) 
                    ->setNom('OTA')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $client = new Client();
            $client 
                    ->setId(2) 
                    ->setNom('ATM')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');
            
            $client = new Client();
            $client 
                    ->setId(3) 
                    ->setNom('AT')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $client = new Client();
            $client 
                    ->setId(4) 
                    ->setNom('Ooredoo')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $client = new Client();
            $client 
                    ->setId(5) 
                    ->setNom('CITAL')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $client = new Client();
            $client 
                    ->setId(6) 
                    ->setNom('SONATRACH')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $client = new Client();
            $client 
                    ->setId(7) 
                    ->setNom('SNC LAVALIN')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $client = new Client();
            $client 
                    ->setId(8) 
                    ->setNom('NOKIA')
                    ;
            $manager->persist($client);
            $output->write('<comment>#</comment>');

            $manager->persist($client);
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            

            // User ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : User : </question>');
            $group = $repository->getRepository('AppBundle:Groupes')->findOneByGroupname('USER');
            $reqs = $dbs->prepare("SELECT * FROM employe Order By code_employe ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {   
                $email = $recs["IDemploye"].'@rtie-dz.com';
                $email= strtolower($email);
                $user = new User();
                $user   ->setId($recs["code_employe"])
                        ->setUsername($recs["IDemploye"])
                        ->setEmail($email)
                        ->setPassword($encoder->encodePassword($user, 'pass'))
                        ->setActive($recs["active"])
                        ->setMission($recs["active_mission"])
                        ->setGroupes($group)
                        ->setNaissance(new DateTime($recs["date_nais"]))
                        ->setNom($recs["nom_employe"])
                        ;
                $manager->persist($user);
                $output->write('<comment>#</comment>');

            }
            $manager->persist($groupes);
            try 
            {
                $manager->flush();
            } catch (\Throwable $th) 
            {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            
            // Zone ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Zone : </question>');
            $reqs = $dbs->prepare("SELECT * FROM zone Order By zone");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                $zone = new Zone();
                $zone   ->setId($recs['zone'])
                        ->setNom("Zone_".$recs['zone'])
                ;
                $manager->persist($zone);
                $output->write('<comment>#</comment>');
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            
            // Wilaya ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Wilaya : </question>');
            $reqs = $dbs->prepare("SELECT * FROM wilaya Order By code_wilaya");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                $zone = $repository->getRepository("AppBundle:Zone")->find($recs["zone_wilaya"]);
                $wilaya = new Wilaya();
                $wilaya ->setId($recs['code_wilaya'])
                        ->setNom($recs['nom_wilaya'])
                        ->setZone($zone)
                        ->setMontantFm(0)
                ;
                $manager->persist($wilaya);
                $output->write('<comment>#</comment>');
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }    
        
            // Site ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Site : </question>');

            $reqs = $dbs->prepare("ALTER TABLE site ADD client int");
            $reqs->execute();

            $reqs = $dbs->prepare("UPDATE site SET client = 0 WHERE client IS NULL");
            $reqs->execute();

            //  OTA 
            $reqs = $dbs->prepare("UPDATE site SET client = 1 
                                    WHERE (
                                    ((code_site REGEXP '^[aco][0-9]{2}[sxmbt]|^[a-z \-]+$|^msc[0-9]+$|^[a-z \-]+$|^pk[0-9]+$' ) 
                                    and (not (code_site REGEXP '^ct |^ca ' )) 
                                    and (code_site not in ('PY','ZCINA','Arzew','CAROTHEQUECINA','CIS','AOP','CINA','TD-HMD')) 
                                    and (code_site not in ('Boutique Orredoo','Boutique orredoo bej','WH WTA Oran','WH WTA Rouiba'))
                                    and (code_site not in ('centrale electrique'))
                                    and (code_site not in ('chaiba','Constantine NMS','DDO','Draa el mizane','el kala','EMRT Blida','HOUCINE DEY','WH AT'))
                                    and (code_site not in ('Hydra mobilis'))
                                    and (code_site not in ('LTPCC','LSI du depot','Annaba Cclt'))
                                    and (code_site not in ('Alcatel'))
                                    )
                                    or (code_site in ('WH_OTA','WH OTA','WH-OTA Constantine')))
                                    and ( client =0 ) 
                                    ");
            $reqs->execute();
            $output->writeln('<comment>Site OTA = Ok.</comment>');

            // ATM Mobilis
            $reqs = $dbs->prepare("UPDATE site SET client = 2
                                    WHERE (code_site REGEXP '^[0-9]+$' or code_site = 'PY' ) 
                                    or (code_site in ('Hydra mobilis'))
                                    and ( client =0 )
                                    ");
            $reqs->execute();  
            $output->writeln('<comment>Site ATM Mobilis = Ok.</comment>');
        

            // AT
            $reqs = $dbs->prepare("UPDATE site SET client = 3
                                    WHERE ((code_site  REGEXP '^ct|^ca|^sp[0-9]+$')
                                    or(code_site in ('SR ADL / HONET OBN C','TCC11','WH AT','Hydra mobilis','chaiba','Constantine NMS','DDO','Draa el mizane','el kala','EMRT Blida','HOUCINE DEY','WH AT','rep03','rep06')))
                                    and ( client =0 )
                                    ");
            $reqs->execute();
            $output->writeln('<comment>Site AT = Ok.</comment>');


            // Ooredoo ********************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 4
                                    WHERE ((code_site  REGEXP '^al[0-9]+$|^alt[0-9]+$|^ans[0-9]+$|^bas[0-9]+$|^to[0-9]+$|^tp[0-9]+$|^ts[0-9]+$|^bat[0-9]+$|^sos[0-9]+$|^se[0-9]+$|^set[0-9]+$|^sks[0-9]+$|^ai[0-9]+$|^bj[0-9]+$|^bjt[0-9]+$|^bl[0-9]+$|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob|^t[osp]t[0-9]+$')
                                    or (code_site in ('Boutique Orredoo','Boutique orredoo bej','WH_WTA','WH WTA Oran','WH WTA Rouiba','A1679','BLT02','MIS21','S0S29')))
                                    and ( client =0 )
                                    ");
            $reqs->execute();
            $output->writeln('<comment>Site Ooredoo = Ok.</comment>');


            // CITAL ***********************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 5
                                    WHERE ((code_site  REGEXP '^p[0-9]+$|^sst[0-9]+$')
                                    or (code_site in ('LTPCC','LSI du depot','Annaba Cclt','Téléphérique','Dépôt BEK')))
                                    and ( client =0 )
                                    ");
            $reqs->execute();       

            // SONATRACH ************************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 6
                                    WHERE ((code_site  REGEXP 'elr1|^pc[0-9]+$|ps[0-9]+$|sta[0-9]+$') 
                                    or (code_site in ('PY','ZCINA','Arzew','CAROTHEQUECINA','CIS','AOP','CINA','TD-HMD','24 FEV','E1C','E2A','O2P','OMN77','OMO13','OMP53','s1a','TA-GR1','U25BIS','U26LR1')))
                                    and ( client =0 )
                                    ");
            $reqs->execute(); 
            $output->writeln('<comment>Site Sonatrach = Ok.</comment>');
        

            // SNC LAVALIN *****************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 7
                                    WHERE (code_site = 'centrale electrique' ) 
                                    and ( client =0 )
                                    ");
            $reqs->execute(); 
            $output->writeln('<comment>Site SNC LAVALIN = Ok.</comment>');
    

            // NOKIA *****************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 8
                                    WHERE (code_site in ('Alcatel','WH_ALU') ) 
                                    and ( client =0 )
                                    ");
            $reqs->execute();  
            // Le reste OTA *****************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 1
                                    WHERE ( client =0 )
                                    ");
            $reqs->execute();  
            $output->writeln('<comment>Site NOKIA = Ok.</comment>');
    

            $reqs = $dbs->prepare("SELECT * FROM site Order By code_site");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $j = 1;
            foreach ($ress as $recs)
            {   
                $wilaya = $repository->getRepository("AppBundle:Wilaya")->find($recs["code_wilaya"]);
                //dump("wilaya: " .$recs["code_wilaya"]);
                $client = $repository->getRepository("AppBundle:Client")->find($recs["client"]);
                $site = new Site();
                $site   ->setId($j)
                        ->setCode($recs["code_site"])
                        ->setNom($recs["nom_site"])
                        ->setNouveau($recs["nouveau"])
                        ->setWilaya($wilaya)
                        ->setClient($client)
                        ;
                $manager->persist($site);
                $output->write('<comment>#</comment>');
                $j++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            
            
            // Mission ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Mission : </question>');
            $reqs = $dbs->prepare("SELECT * FROM mission Order By code_mission ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $j=1;
            foreach ($ress as $recs)
            {
                $user = $repository->getRepository("AppBundle:User")->find($recs["code_employe"]);
                $mission    = new Mission();
                $mission    ->setId($j)
                            ->setCode($recs["code_mission"])
                            ->setDepart(new DateTime($recs["date_depart"]))
                            ->setRetour(new DateTime($recs["date_retour"]))
                            ->setvEmploye($recs["validation_employe"])
                            ->setvRollout($recs["validation_rollout"])
                            ->setvComptabilite($recs["validation_comp"])
                            ->setAvance($recs["mon_avance"])
                            ->setUser($user)
                            ;
                $manager->persist($mission);
                $output->write('<comment>#</comment>');
                $j++;

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            // Vehicule ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Vehicule : </question>');
            $reqs = $dbs->prepare("SELECT * FROM vehicule Order By id");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                $vehicule    = new Vehicule();
                $vehicule   ->setId($recs["id"])
                            ->setNom($recs["nom"])
                            ->setActive($recs["active"])
                            ;
                $manager->persist($vehicule);
                $output->write('<comment>#</comment>');

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            // Projet ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Projet : </question>');
            $reqs = $dbs->prepare("SELECT * FROM projet Order By code_projet ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                $projet    = new Projet();
                $projet ->setId($recs["code_projet"])
                        ->setNom($recs["des_projet"])
                        ;
                $manager->persist($projet);
                $output->write('<comment>#</comment>');
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            
            // Sous Projet ***************************************************************************************************************************
            $k=20;
            do {
                $k++;
                $reqs = $dbs->prepare("SELECT * FROM sous_projet WHERE code_sous_projet =" . $k);
                $reqs->execute();
                $ress = $reqs->fetchAll();
            } while (count($ress) == 1);

            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Sous Projet : </question>');
            $reqs = $dbs->prepare("SELECT * FROM sous_projet Order By code_sous_projet ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                if ($recs["code_sous_projet"] != 31)
                {   
                    if ($recs["code_sous_projet"] == 0)
                    {
                        $vcode_sous_projet = $k;
                    }else
                    {
                        $vcode_sous_projet = $recs["code_sous_projet"];
                    }

                    $projet = $repository->getRepository('AppBundle:Projet')->find($recs['code_projet']);
                    $sousProjet     = new SousProjet();
                    $sousProjet     ->setId($vcode_sous_projet)
                                    ->setNom($recs["des_sous_projet"])
                                    ->setProjet($projet)
                                ;
                    $manager->persist($sousProjet);
                    $output->write('<comment>#</comment>');
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
        
            // Prestation ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Prestation : </question>');
            
            $reqs = $dbs->prepare("SELECT * FROM prestation WHERE code_sous_projet IN ( 
                SELECT code_sous_projet FROM sous_projet) Order By code_prestation ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                $sousProjet = $repository->getRepository('AppBundle:SousProjet')->find($recs['code_sous_projet']);
                $prestation     = new Prestation();
                $prestation     ->setId($recs["code_prestation"])
                                ->setNom($recs["des_prestation"])
                                ->setSousProjet($sousProjet)
                            ;
                $manager->persist($prestation);
                $output->write('<comment>#</comment>');

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
        
            //*** Intervention ***************************************************************************************************************************
            $i++;
            // Enlever Cle ID, ajout Method SetId()
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Intervention : </question>');
            $reqs = $dbs->prepare("SELECT * FROM intervention Order By code_intervention ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                //$mission = $repository->getRepository('AppBundle:Mission')->findOneBy(['code_mission' => $recs['code_mission']]);
                $mission = $repository->getRepository('AppBundle:Mission')->findOneBy(['code' => $recs['code_mission']]);
                $site = $repository->getRepository('AppBundle:Site')->findOneBy(['code' => $recs['code_site']]);
                $prestation = $repository->getRepository('AppBundle:Prestation')->find($recs['code_prestation']);
                $vehicule = $repository->getRepository('AppBundle:Vehicule')->find($recs['id_vehicule']);

                $destination    = new Intervention();
                $destination    ->setId($recs["code_intervention"])
                                ->setMission($mission)
                                ->setSite($site)
                                ->setPrestation($prestation)
                                ->setDateReception(new DateTime($recs['date_reception_tache']))
                                ->setDateIntervention(new DateTime($recs['date_intervention']))
                                ->setDesignation($recs['des_intervention'])
                                ->setQuantite($recs['quantite'])
                                ->setVehicule($vehicule)
                                ->setTarif(0)

                            ;
                $manager->persist($destination);
                $output->write('<comment>#</comment>');

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Famille depense ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Famille Depense : </question>');
            //$group = $repository->getRepository('AppBundle:Groupes')->find(40);
            $reqs = $dbs->prepare("SELECT * FROM famille_dep Order By code_fam_dep ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {
                $destination    = new FamilleDepense();
                $destination    ->setId($recs["code_fam_dep"])
                                ->setNom($recs["des_fam_dep"]);
                $manager->persist($destination);
                $output->write('<comment>#</comment>');            

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Depense ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Depense : </question>');
            // Enlever Cle ID, ajout Method SetId()
            
            $reqs = $dbs->prepare("UPDATE depense SET code_depense = 48 WHERE code_depense = 87 ");
            $reqs->execute();

            $reqs = $dbs->prepare("SELECT * FROM table_depense WHERE (code_depense != 87) Order By code_depense ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {   $familleDepense = $repository->getRepository('AppBundle:FamilleDepense')->find($recs['code_fam_dep']);
                $destination    = new Depense();
                $destination    ->setId($recs['code_depense'])
                                ->setNom($recs["des_depense"])
                                ->setNouveau($recs['nouveau'])
                                ->setFamilleDepense($familleDepense)
                            ;
                $manager->persist($destination);
                $output->write('<comment>#</comment>');

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Justification Depense ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Justification Depense : </question>');
            $reqs = $dbs->prepare("SELECT * FROM jus_depense Order By code_jus ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {   //$familleDepense = $repository->getRepository('AppBundle:FamilleDepense')->find($recs['code_fam_dep']);
                $destination    = new JustificationDepense();
                $destination    ->setId($recs["code_jus"])
                                ->setNom($recs["des_jus"])
                            ;
                $manager->persist($destination);
                $output->write('<comment>#</comment>');
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Depense Mission ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : >Depense Mission : </question>');
            // Enlever Cle ID, ajout Method SetId()
            // Remplire la table Carburant créer nouvellement
            // 2 : sans plan,   3 : gas-oil,    4 : essance
            // 1 : sans plan ,  2 : gas-oil ,   3 : essance
            $carburant = new Carburant();
            $carburant  ->setId(1)
                        ->setDesignation("Sans plan")
                        ;
            $manager->persist($carburant);

            $carburant = new Carburant();
            $carburant  ->setId(2)
                        ->setDesignation("Gas-oil")
                        ;
            $manager->persist($carburant);

            $carburant = new Carburant();
            $carburant  ->setId(3)
                        ->setDesignation("Essence")
                        ;
            $manager->persist($carburant);
            $manager->flush();

            $reqs = $dbs->prepare("SELECT * FROM depense Order By id_depense ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {   
                $mission = $repository->getRepository('AppBundle:Mission')->findOneBy(["code" => $recs["code_mission"]]);
                $justification = $repository->getRepository('AppBundle:JustificationDepense')->find($recs['code_jus']);
                $val = $recs['code_depense'];
                if (in_array($val, ['2','3','4'])) { // Si c'est carburant on va le enregistre dans CarburantMission
                    $carburant = $repository->getRepository('AppBundle:Carburant')->find($recs['code_depense']-1);
                    $intervention = $repository->getRepository('AppBundle:Intervention')->findOneByMission($mission);
                    if ($intervention != null) // Mission on lui affecte un vehicule de l'intervention
                    {
                        $vehicule = $intervention->getVehicule();

                    } else // Charge on lui affecte Toyota A pour Gas-oil et Ford A pour essance
                    {   
                        if ($carburant->getId() == 2){ // Gas-oil
                            $vehicule = $repository->getRepository('AppBundle:Vehicule')->find(2); // Toyata A
                        }else { // Essance
                            $vehicule = $repository->getRepository('AppBundle:Vehicule')->find(3); // Ford A A
                        }
                    }
                    $carburantMission = new CarburantMission();
                    $carburantMission   ->setMission($mission)
                                        ->setVehicule($vehicule)
                                        ->setCarburant($carburant)
                                        ->setJustificationDepense($justification)
                                        ->setKms(0)
                                        ->setMontant($recs["mon_depense"])
                                        ->setDate(new Datetime($recs['date_depense']))
                                        ->setObs($recs["obs_depense"])
                                ;
                    $manager->persist($carburantMission);
                    $output->write('<comment>#</comment>');
                }else  // Si depense non carburant enregistre dans DepenseMission
                { 
                    $depense = $repository->getRepository('AppBundle:Depense')->find($recs['code_depense']);
                    $depenseMission = new DepenseMission();
                    $depenseMission ->setId($recs['id_depense'])
                                    ->setDepense($depense)
                                    ->setMission($mission)
                                    ->setJustificationDepense($justification)
                                    ->setMontant($recs["mon_depense"])
                                    ->setDatedep(new Datetime($recs['date_depense']))
                                    ->setObs($recs["obs_depense"])
                                ;
                    $manager->persist($depenseMission);
                    $output->write('<comment>#</comment>');
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            //*** Correction du carburant pour vehicle
            //*** Essance pour Ford A et Gas-oil pour Toyaota A
        
            $reqd = $dbd->prepare("UPDATE  carburant_mission SET vehicule_id = 3  WHERE vehicule_id in(1,2,6,7,8,9) and carburant_id in(1,3)");
            $reqd->execute();

            $reqd = $dbd->prepare("UPDATE  carburant_mission SET vehicule_id = 1  WHERE vehicule_id in(3,4,5) and carburant_id = 2 ");
            $reqd->execute();
        
            //*** Frais Mission ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Frais Mission : </question>');

            $reqs = $dbs->prepare("SELECT * FROM frais_mission Order By id_frais_mission ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {   $mission = $repository->getRepository('AppBundle:Mission')->findOneBy(["code" => $recs['code_mission']]);
                $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $destination    = new FraisMission();
                $destination    ->setMission($mission)
                                ->setUser($user)
                                ->setDateFm(new DateTime($recs["date_frais_mission"]))
                                ->setMontant($recs["mon_frais_mission"])
                            ;
                $manager->persist($destination);
                $output->write('<comment>#</comment>');

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Intervention user ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Intervention user : </question>');

            $reqs = $dbs->prepare("SELECT * FROM realisateur_intervention ");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            foreach ($ress as $recs)
            {   $intervention = $repository->getRepository('AppBundle:Intervention')->find($recs['code_intervention']);
                $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $interventionUser   = new InterventionUser();
                $interventionUser   ->setIntervention($intervention)
                                    ->setUser($user)
                                    ;
                $manager->persist($interventionUser);
                $output->write('<comment>#</comment>');

            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Tarif Prestation ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Tarif Prestation : </question>');

            $reqs = $dbs->prepare("SELECT * FROM tarif_prestation Order By zone ASC, code_prestation ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();

            foreach ($ress as $recs)
            {   $prestation = $repository->getRepository('AppBundle:Prestation')->find($recs['code_prestation']);
                $zone = $repository->getRepository('AppBundle:Zone')->find($recs['zone']);
                $tarifPrestation    = new TarifPrestation();
                $tarifPrestation    ->setPrestation($prestation)
                                    ->setZone($zone)
                                    ->setMontant($recs["tarif_prestation"])
                            ;
                $manager->persist($tarifPrestation);
                $output->write('<comment>#</comment>');
            }

            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Fonction ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Fonction : </question>');

            $reqs = $dbs->prepare("SELECT * FROM fonction Order By code_fonction ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();

            foreach ($ress as $recs)
            {   
                $fonction   = new Fonction();
                $fonction   ->setId($recs["code_fonction"])
                            ->setNom($recs["des_fonction"])
                            ;
                $manager->persist($fonction);
                $output->write('<comment>#</comment>');
            }

            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
    

            //*** Fonction User***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Fonction User : </question>');

            $reqs = $dbs->prepare("SELECT * FROM fonction_employe Order By date_fonction ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();

            foreach ($ress as $recs)
            {   $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $fonction = $repository->getRepository('AppBundle:Fonction')->find($recs['code_fonction']);
                $fonctionUser   = new FonctionUser();
                $fonctionUser   ->setUser($user)
                                ->setFonction($fonction)
                                ->setDatefonction(new Datetime($recs['date_fonction']))
                            ;
                $manager->persist($fonctionUser);
                $output->write('<comment>#</comment>');
            }

            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }


            //*** Recrutement ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Recrutement : </question>');

            $reqs = $dbs->prepare("SELECT * FROM recrutement Order By date_recrutement ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();

            foreach ($ress as $recs)
            {   $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $recrutement   = new Recrutement();
                $recrutement   ->setUser($user)
                                ->setRecruter(new Datetime($recs['date_recrutement']))
                            ;
                $manager->persist($recrutement);
                $output->write('<comment>#</comment>');
            }

            try 
            {
                $manager->flush();
            } catch (\Throwable $th) 
            {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

          

            //*** Pointage ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Pointage : </question>');

            $reqs = $dbs->prepare("SELECT * FROM pointage_designation Order By des_pointageID ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();

            foreach ($ress as $recs)
            {   $pointage   = new Pointage();
                $pointage   ->setId($recs['des_pointageID'])
                            ->setDesignation($recs['des_pointage'])
                            ;
                $manager->persist($pointage);
                $output->write('<comment>#</comment>');
            }

            try 
            {
                $manager->flush();
            } catch (\Throwable $th) 
            {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

            //*** Poinatge Employe  ***************************************************************************************************************************
            $i++;
            $output->writeln('');
            $output->writeln('<question>' . $i . ' : Pointage Employe : </question>');

            $reqs = $dbs->prepare("SELECT * FROM pointage Order By date_pointage ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();

            $j=1;
            foreach ($ress as $recs)
            {   $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $pointage = $repository->getRepository('AppBundle:Pointage')->find($recs['des_pointageID']);
                $pointageUser   = new PointageUser();
                $pointageUser   ->setId($j)
                                ->setUser($user)
                                ->setPointage($pointage)
                                ->setDate(new Datetime($recs['date_pointage']))
                                ->setHtravail($recs['h_travail'])
                                ->setHRoute($recs['h_route'])
                                ->setHSup($recs['h_sup'])
                                ->setObs($recs['obs_pointage'])
                            ;
                $manager->persist($pointageUser);
                $output->write('<comment>#</comment>');
                $j++;
            }

            try 
            {
                $manager->flush();
            } catch (\Throwable $th) 
            {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }

        } //*** Fin */ 


        $output->writeln('');
        $output->writeln('');
        $output->writeln('<comment>===>  Copier les Entity avec Auto Incrementation dans src/Entity  <comment>');
        $output->writeln('<comment>===>  Utiliser : php bin/console doctrine:schema:drop --dump-sql</comment>');
        $output->writeln('<comment>===>  fier le code : Creation de id Auto en premier puis les contraintes en dernier <comment>');
        $output->writeln('<comment>===>  Executer le code ALTER TABLE ... DROP FOREIGN KEY ... dans PhpMyAdmin <comment>');
        $output->writeln('<comment>===>  Excuter php bin/console doctrine:schema:update --dump-sql');
        $output->writeln('<comment>===>  Executer ALTER TABLE ... CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE ... <comment>');
        $output->writeln('<comment>===>  Executer ALTER TABLE ... ADD CONSTRAINT ... FOREIGN KEY ... <comment>');
        $output->writeln('');
        $output->writeln('********************************');
        $output->writeln('****** Fin de Traitement *******');
        $output->writeln('********************************');
        $output->writeln('');
    }
}
