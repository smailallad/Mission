<?php
namespace AppBundle\Command;
use PDO;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpgradeRtieCommand extends ContainerAwareCommand
{   // php bin/console UpgradeRtie
    private $entityManager;
    private $encoderInterface;

    public function __construct(EntityManagerInterface $entityManager,UserPasswordEncoderInterface $encoderInterface)
    {
        $this->entityManager = $entityManager;
        $this->encoderInterface = $encoderInterface;

        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setName('upgradeRtie')
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
        //$output->writeln('<info>info</info>');
        // white text on a red background
        //$output->writeln('<error>error</error>');
        $argument = $input->getArgument('argument');
        
        if ($input->getOption('option')) {
            // ...
        }

        $dbs = new \PDO("mysql:host=localhost;dbname=dbrtie;charset=utf8", "smail", "Ads2160396!");
        $dbd = new \PDO("mysql:host=localhost;dbname=rtie3.4;charset=utf8", "smail", "Ads2160396!");
        $dbv = new \PDO("mysql:host=localhost;dbname=dbvehicule;charset=utf8", "smail", "Ads2160396!");
        $dirEntity = "/var/www/html/rtie3.4/src/AppBundle/Entity/";
        
        //$manager = $this->entityManager;
        $encoder = $this->encoderInterface;
        //$manager = $manager->getRepository("App:SomeEntity");

        //$manager = $this->entityManager->getManager();
        //$manager = $this->entityManager;
        //$encoder = $this->getContainer()->get('security.password_encoder');
        $nullOutput = new NullOutput();
       
        $output->writeln('');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',50). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' Début de traitemet',51). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',50). '</>');
        $output->writeln('');
        //$output->writeln('<comment>===>  Executer la commande : php bin/console restore <comment>');
       
        //goto fin;
        //goto test;
        
        //#############################################################################################################"
        // Vider DB rtie3.4
        //#############################################################################################################"
        {
            $output->write('<info>' . str_pad('Vider DB rtie3.4',35,'.') . ': </info>');
            $dbd->prepare('SET foreign_key_checks = 0')->execute();
            $sql = 'SHOW TABLES';
            $reqd = $dbd->prepare($sql);
            $reqd->execute();
            $recd = $reqd->fetchAll();
            $k=0;
            foreach ($recd as $table){
                $sql = "DROP TABLE ".$table[0];
                //dump($sql);
                $reqd = $dbd->prepare($sql);
                if (!$reqd->execute()) {
                    $output->writeln('');
                    $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
                }else {
                    $output->write('<comment>#</comment>');
                    $k++;
                }
            }
            $dbd->prepare('SET foreign_key_checks = 1')->execute();
            $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        }
        //#############################################################################################################"
        // Suppresion DB dbrtie
        //#############################################################################################################"
        {
            $output->write('<info>' . str_pad('Suppresion DB dbrtie',35,'.') . ': </info>');
            $dbs->prepare('SET foreign_key_checks = 0')->execute();
            $sql = 'SHOW TABLES';
            $reqs = $dbs->prepare($sql);
            $reqs->execute();
            $recs = $reqs->fetchAll();
            $k=0;
            foreach ($recs as $table){
                $sql = "DROP TABLE ".$table[0];
                //dump($sql);
                $reqs = $dbs->prepare($sql);
                if (!$reqs->execute()) {
                    $output->writeln('');
                    $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
                }else{
                    $output->write('<comment>#</comment>');
                    $k++;
                }
            }
            $dbs->prepare('SET foreign_key_checks = 1')->execute();
            $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        }
        //#############################################################################################################"
        // Copier DB dbrtie_backup vers dbrtie
        //#############################################################################################################"
        
        $output->write('<info>' . str_pad('Copier DB dbrtie',35,'.') . ': </info>');
        shell_exec("mysqldump --user=smail --password=Ads2160396! dbrtie_backup | mysql --user=smail --password=Ads2160396! dbrtie");

        //$output->writeln('<comment>'.str_pad(' ',32). str_pad('#',50,'#').' 100%.</comment>');  
    
        //#############################################################################################################"
        // Remove Auto Incrementation et Id dans les Entity
        //#############################################################################################################"
        
        $output->write('<info>' . str_pad('Remove Auto Incrimentation',35,'.') . ': </info>');
        $find='@ORM\GeneratedValue';
        $replace='ORM\GeneratedValue';
        //$findId='ORM\Id';
        //$replaceId='@ORM\Id';

        $scandir = scandir($dirEntity);
        foreach($scandir as $fichier){
            if(substr(strtolower($fichier),-4,4)==".php"){
                $file = $dirEntity.$fichier;
                $str = file_get_contents($file);
                $str = str_replace($find, $replace, $str);
                //$str = str_replace($findId, $replaceId, $str);
                file_put_contents($file, $str);
                //$output->writeln('<info>' . str_pad($fichier,35,'.').'</info> <comment> : Remove GeneratedValue</comment>' );
            }
        }
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');
        
        
        //#############################################################################################################"
        // Generate et dump entities 
        //#############################################################################################################"
    
        //symfony console doctrine:generate:entities
        $output->write('<info>' . str_pad('Generate entities',35,'.') . ': </info>');
        shell_exec("symfony console doctrine:generate:entities AppBundle");
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');

        //############################################################################################################"
        // Add Id au fichier Entity
        //#############################################################################################################"
    
        /*$output->writeln('<info>' . str_pad('Add Auto Incrimentation',35,'.') . ': </info>');
        $findId='ORM\Id';
        $replaceId='@ORM\Id';

        $scandir = scandir($dirEntity);
        foreach($scandir as $fichier){
            if(substr(strtolower($fichier),-4,4)==".php"){
                $file = $dirEntity.$fichier;
                $str = file_get_contents($file);
                $str = str_replace($find, $replace, $str);
                $str = str_replace($findId, $replaceId, $str);
                file_put_contents($file, $str);
                $output->writeln('<info>' . str_pad($fichier,35,'.').'</info> <comment> : Add GeneratedValue</comment>' );
            }
        }*/
        
        $output->write('<info>' . str_pad('doctrine:schema:update --dump-sql',35,'.') . ': </info>');
        shell_exec("symfony console doctrine:schema:update --dump-sql");
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');

        $output->write('<info>' . str_pad('doctrine:schema:update --force',35,'.') . ': </info>');
        shell_exec("symfony console doctrine:schema:update --force");
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');

        //#############################################################################################################"
        // DROP FOREIGN KEY Destination
        //#############################################################################################################"
        
        $output->write('<info>' . str_pad('DROP FOREIGN KEY',35,'.') . ': </info>');
        $sql='  SELECT TABLE_NAME,CONSTRAINT_NAME
        FROM   INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE  REFERENCED_TABLE_SCHEMA = "rtie3.4";';
        $q = $dbd->prepare($sql);
        $q->execute();
        $tables = $q->fetchAll();
        $k=0;
        foreach ($tables as $table) {
            $sql = 'ALTER TABLE ' . $table['TABLE_NAME'] . ' DROP FOREIGN KEY ' . $table['CONSTRAINT_NAME'];
            $reqd = $dbd->prepare($sql);
            if (!$reqd->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
            }else{
                $output->write('<comment>#</comment>');   
                $k++; 
            }
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>'); 
        
        //#############################################################################################################"
        // Upgrade
        //#############################################################################################################"

        // Roles **********************************************************************************************************
        $output->write('<info>' . str_pad('Roles',35,'.'). ': </info>');
        $id=1;
        $sql ="INSERT INTO roles (id,rolename) ";
        $sql .="VALUES (" . $id .",'ROLE_ADMIN'),";$groupeAdmin= $id; $id++; 
        $sql .="(" . $id .",'ROLE_GERANT'),";$id++;           
        $sql .="(" . $id .",'ROLE_FACTURATION'),";$id++;      
        $sql .="(" . $id .",'ROLE_SUPER_COMPTABLE'),";$id++;  
        $sql .="(" . $id .",'ROLE_COMPTABLE'),";$id++;        
        $sql .="(" . $id .",'ROLE_ADMINISTRATION'),";$id++;   
        $sql .="(" . $id .",'ROLE_ROLLOUT'),";$id++;          
        $sql .="(" . $id .",'ROLE_BUREAU'),";$id++;           
        $sql .="(" . $id .",'ROLE_CHEF_PARK'),";$id++;        
        $sql .="(" . $id .",'ROLE_USER')";$groupeUser = $id;$id++;
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');
        
        // Groupes ***************************************************************************************************************************
        $output->write('<info>' . str_pad('Groupes',35,'.'). ': </info>');
        $id=1;
        $sql ="INSERT INTO groupes (id,roles,groupname) ";
        $sql .="VALUES (" . $id . ",'" . serialize(['ROLE_ADMIN']) . "','ADMIN'),"; $id++; 
        $sql .="(" . $id .",'" . serialize(['ROLE_GERANT']) . "','GERANT'),"; $id++;
        $sql .="(" . $id .",'" . serialize(['ROLE_FACTURATION']) . "','FACTURATION'),"; $id++;
        $sql .="(" . $id .",'" . serialize(['ROLE_SUPER_COMPTABLE']) . "','SUPER_COMPTABLE'),"; $id++;
        $sql .="(" . $id .",'" . serialize(['ROLE_COMPTABLE']) . "','COMPTABLE'),"; $id++;
        $sql .="(" . $id .",'" . serialize(['ROLE_ADMINISTRATION']) . "','ADMINISTRATION'),"; $id++; 
        $sql .="(" . $id .",'" . serialize(['ROLE_ROLLOUT']) . "','ROLLOOT'),"; $id++; 
        $sql .="(" . $id .",'" . serialize(['ROLE_BUREAU']) . "','BUREAU'),"; $id++;
        $sql .="(" . $id .",'" . serialize(['ROLE_CHEF_PARK']) . "','CHEF_PARK'),"; $id++;
        $sql .="(" . $id .",'" . serialize(['ROLE_USER']) . "','USER')"; $id++;
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }else{
            //$output->write('<comment>#</comment>');   
            $id++; 
        }
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');
        
        // User *****************************************************************************************************************
        $output->write('<info>' . str_pad('User',35,'.'). ': </info>');
        //$groupUser = $manager->getRepository('App:Groupes')->findOneByGroupname('USER');
        //$groupAdmin = $manager->getRepository('App:Groupes')->findOneByGroupname('ADMIN');
        $reqs = $dbs->prepare("SELECT * FROM employe Order By code_employe ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql ="";
        foreach ($ress as $recs)
        {   $nom = $recs["nom_employe"];
            $nom = explode(" ",$nom);
            $email = "";
            for ($i=(count($nom)-1); $i >=0 ; $i--) { 
                $email = $email == "" ? $email = strtolower($nom[$i]) : $email .=  "." . strtolower($nom[$i]);
            }
            $email= $email . "@rtie-dz.com";
            $user = new User();
            $password =$encoder->encodePassword($user, 'pass');
            //$password = addslashes($password);
            $recs["code_employe"] == 2 ? $groupe = $groupeAdmin : $groupe = $groupeUser ;
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" . $recs["code_employe"].",".$groupe.",'".$recs["IDemploye"]."','".$recs["nom_employe"]."','".$email."','".$password."','".$recs["date_nais"]."',".$recs["active"].",".$recs["active_mission"]. ")";
            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $k++;
                $c = 1;
            }else{
                $c++;
            }
        }
        $sql ="INSERT INTO user (id,groupes_id,username,nom,email,password,naissance,active,mission) VALUES " . $sql .";";
        
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Client *******************************************************************************************
        $output->write('<info>' .str_pad('Client',35,'.') .': </info>');
        $reqs = $dbs->prepare("SELECT * FROM projet Order By code_projet ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" . $recs["code_projet"].",'".$recs["des_projet"]. "')";$output->write('<comment>#</comment>');$k++;
        }
        $sql ="INSERT INTO client (id,nom) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%</comment>');
        
        // Client facturation *************************************************************************************************
        $output->write('<info>' . str_pad('Client Facturation',35,'.'). ': </info>');
        $sql = "INSERT INTO client_facturation (id,nom,adresse,telephone,rc,tin,nif) VALUE (1,'Alcatel-Lucent International Algérie','14, Avenue des frères Bouadou 16005 Bir Mourad Rais, Alger','021 44 77 66','16/00-07S0973859','00000085952','000716097385968')";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%</comment>');
        
        // Zone ***************************************************************************************************************
        $output->write('<info>' . str_pad('Zone',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM zone Order By zone");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" . $recs["zone"].",'".$recs["zone"]. "')";
            $output->write('<comment>#</comment>');
            $k++;
            
        }
        $sql ="INSERT INTO zone (id,nom) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Wilaya ***********************************************************************************************************
        $output->write('<info>' . str_pad('Wilaya',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM wilaya Order By code_wilaya");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {
            //chercher les frais de mission
            $fm = 0;
            $sql1 = "SELECT * FROM frais_mission_wilaya WHERE code_wilaya = " . $recs["code_wilaya"];
            $reqs1 = $dbs->prepare($sql1);
            if (!$reqs1->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
            }
            $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
            if ($resc1 !== false)
            {
                $fm = $resc1["mon_f_mission"];
            } 
            $code = str_pad($recs["code_wilaya"], 2, "0", STR_PAD_LEFT); 
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" . $recs["code_wilaya"].",'".$code."','".addslashes($recs["nom_wilaya"])."',".$recs["zone_wilaya"].",".$fm. ")";
            $output->write('<comment>#</comment>');
            $k++;
        }
        $sql ="INSERT INTO wilaya (id,code,nom,zone_id,montantFm) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }  
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Site *****************************************************************************************************************
        $output->write('<info>' . str_pad('Site',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM site Order By code_site");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $j = 1;
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" . $j.",'".$recs["code_site"]."','".addslashes($recs["nom_site"])."',".$recs["nouveau"].",".$recs["code_wilaya"].",".$recs["client"]. ")";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
            $j++;
        }
        
        $sql ="INSERT INTO site (id,code,nom,nouveau,wilaya_id,client_id) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Mission **********************************************************************************************************
        $output->write('<info>' . str_pad('Mission',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM mission Order By code_mission ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $j=1;
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" . $j.",'".$recs["code_mission"]."','".$recs["date_depart"]."','".$recs["date_retour"]."',".$recs["validation_employe"].",".$recs["validation_rollout"].",".$recs["validation_comp"].",".$recs["mon_avance"].",".$recs["code_employe"]. ")";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
            $j++;
        }
        
        $sql ="INSERT INTO mission (id,code,depart,retour,vEmploye,vRollout,vComptabilite,avance,user_id) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //*** Gestion des vehicules 
        //Marque  ***********************************************************************************************************
        $output->write('<info>' . str_pad('Marque',35,'.'). ': </info>');
        $reqs = $dbv->prepare("SELECT * FROM marque Order By id ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["id"].",'".addslashes($recs["nom"])."',true)";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }
        
        $sql ="INSERT INTO marque (id,nom,active) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //*** Gestion des vehicules 
        // Vehicule ********************************************************************************************************
        $output->write('<info>' . str_pad('Vehicule',35,'.'). ': </info>');
        $reqs = $dbv->prepare("SELECT * FROM vehicule Order By id");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["id"].",".$recs["marque_id"].",'".addslashes($recs["nom"])."','".$recs["matricule"]."',".$recs["active"].",".$recs["nbrj_alert_relever"].",".$recs["kms_relever"].",'".$recs["date_relever"]."','".$recs["debut_assurance"]."','".$recs["fin_assurance"]."','".$recs["debut_control_tech"]."','".$recs["fin_control_tech"]."','".addslashes($recs["obs_assurance"])."','".addslashes($recs["obs_control_tech"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }
        $sql ="INSERT INTO vehicule (id,marque_id,nom,matricule,active,nbrj_alert_relever,kms_relever,date_relever,debut_assurance,fin_assurance,debut_control_tech,fin_control_tech,obs_assurance,obs_control_tech) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //*** Gestion des vehicules 
        // InterventionVehicule *****************************************************************************************
        $output->write('<info>' . str_pad('Intervention Vehicule',35,'.'). ': </info>');
        $reqs = $dbv->prepare("SELECT * FROM intervention_vehicule Order By id ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["id"].",'".addslashes($recs["designation"])."','".addslashes($recs["unite"])."',".$recs["important"].")";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO intervention_vehicule (id,designation,unite,important) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //*** Gestion des vehicules 
        // Entretien Vehicule **********************************************************************************************
        $output->write('<info>' . str_pad('Entretion Vehicule',35,'.'). ': </info>');
        $reqs = $dbv->prepare("SELECT * FROM entretien_vehicule Order By id ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["id"].",".$recs["vehicule_id"].",".$recs["user_id"].",'".$recs["date"]."',".$recs["kms"].",'".addslashes($recs["obs"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO entretien_vehicule (id,vehicule_id,user_id,date,kms,obs) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //*** Gestion des vehicules 
        // KmsInterventionVehicule *********************************************************************************************
        $output->write('<info>' . str_pad('Kms Vehicule',35,'.'). ': </info>');
        $reqs = $dbv->prepare("SELECT * FROM kms_intervention_vehicule Order By id ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["id"].",".$recs["marque_id"].",".$recs["intervention_vehicule_id"].",".$recs["kms"].",'".addslashes($recs["obs"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO kms_intervention_vehicule (id,marque_id,intervention_vehicule_id,kms,obs) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //*** Gestion des vehicules 
        // InterventionEntretien ou LigneEntretien **********************************************************************
        $output->write('<info>' . str_pad('Intervention Entretien',35,'.'). ': </info>');
        $reqs = $dbv->prepare("SELECT * FROM intervention_entretien Order By ID ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["ID"].",".$recs["entretien_vehicule_id"].",".$recs["intervention_vehicule_id"].",".$recs["qte"].",'".addslashes($recs["obs"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO intervention_entretien (id,entretien_vehicule_id,intervention_vehicule_id,qte,obs) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }        
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Projet *********************************************************************************************************
        // copier table sous projet dans Entity Projet
        $output->write('<info>' . str_pad('Projet',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM sous_projet Order By code_sous_projet ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {               
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["code_sous_projet"].",".$recs["code_projet"].",1,'".addslashes($recs["des_sous_projet"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO projet (id,client_id,client_facturation_id,nom) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Prestation ***************************************************************************************************************************
        $output->write('<info>' . str_pad('Prestation',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM prestation ORDER BY code_prestation  ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["code_prestation"].",".$recs["code_sous_projet"].",'".addslashes($recs["des_prestation"])."',true)";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO prestation (id,projet_id,nom,active) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Intervention ***********************************************************************************************
        $output->write('<info>' . str_pad('Intervention',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM intervention Order By code_intervention ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   // recupere id mission
            $sql1 = "SELECT * FROM mission WHERE code ='" . $recs["code_mission"]."'";
            $reqs1 = $dbd->prepare($sql1);
            if (!$reqs1->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
            }
            $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
            if ($resc1 !== false)
            {
                $mission = $resc1["id"];
            } 
            // recupere id site
            $sql1 = "SELECT * FROM site WHERE code ='" . $recs["code_site"]."'";
            $reqs1 = $dbd->prepare($sql1);
            if (!$reqs1->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
            }
            $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
            if ($resc1 !== false)
            {
                $site = $resc1["id"];
            } 
            //
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["code_intervention"].",".$mission.",".$site.",".$recs["code_prestation"].",".$recs["id_vehicule"].",'".$recs["date_reception_tache"]."','".$recs["date_intervention"]."','".addslashes($recs["des_intervention"])."','".addslashes($recs["reserve"])."',".$recs["quantite"].")";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO intervention (id,mission_id,site_id,prestation_id,vehicule_id,dateReception,dateIntervention,designation,reserves,quantite) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Famille depense *********************************************************************************************
        $output->write('<info>' . str_pad('Famille',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM famille_dep Order By code_fam_dep ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["code_fam_dep"].",'".addslashes($recs["des_fam_dep"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO famille_depense (id,nom) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Depense *******************************************************************************************
        $output->write('<info>' . str_pad('Depense',35,'.'). ': </info>');
        // Griffe dupliquer (48 et 87) remplacer le 87 par 48.
        $reqs = $dbs->prepare("UPDATE depense SET code_depense = 48 WHERE code_depense = 87 ");
        $reqs->execute();
        $reqs = $dbs->prepare("SELECT * FROM table_depense WHERE (code_depense != 87) Order By code_depense ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["code_depense"].",".$recs["code_fam_dep"].",'".addslashes($recs["des_depense"])."',".$recs["nouveau"].")";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO depense (id,famille_depense_id,nom,nouveau) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Justification Depense ****************************************************************************************
        $output->write('<info>' . str_pad('Justification',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM jus_depense Order By code_jus ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs["code_jus"].",'".addslashes($recs["des_jus"])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO justification_depense (id,nom) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Depense Mission ******************************************************************************************
        
        $output->write('<info>' . str_pad('Depense Mission',35,'.'). ': </info>');
        // Enlever Cle ID, ajout Method SetId()
        // Remplire la table Carburant créer nouvellement
        // 2 : sans plan,   3 : gas-oil,    4 : essance
        // 1 : sans plan ,  2 : gas-oil ,   3 : essance
        $sql = "INSERT INTO carburant (id,nom) VALUE (1,'Sans plan'),(2,'Gas-Oil'),(3,'Essence');";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $reqs = $dbs->prepare("SELECT * FROM depense Order By id_depense ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $id =1;
        $id2 =1;
        $sqlCarburant ="";
        $sqlDepense ="";
        foreach ($ress as $recs)
        {   
            // Recuperer id mission
            $sql1 = "SELECT * FROM mission WHERE code = '".$recs["code_mission"]."'";
            $reqs1 = $dbd->prepare($sql1);
            if (!$reqs1->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
            }
            $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
            if ($resc1 !== false)
            {
                $mission = $resc1["id"];
            }else
            {
                $mission = null;
            }
            // Recuperer id justification_depense 
            $justification = $recs['code_jus'];

            $val = $recs['code_depense'];
            if (in_array($val, ['2','3','4'])) { // Si c'est carburant on va le enregistre dans CarburantMission
                $carburant = $recs['code_depense']-1;
                // Recuperer id de l'intervention
                $sql1 = "SELECT * FROM intervention WHERE mission_id = ".$mission;
                $reqs1 = $dbd->prepare($sql1);
                if (!$reqs1->execute()) {
                    $output->writeln('');
                    $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
                }
                $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
                if ($resc1 !== false) // Mission on lui affecte un vehicule de l'intervention
                {
                    $intervention = $resc1["id"];
                    $vehicule = $resc1["vehicule_id"];
                }else // Charge on lui affecte Toyota A pour Gas-oil et Ford A pour essance
                {
                    $intervention = null;
                    if ($carburant == 2) // Gas-oil
                    {
                        $vehicule = 2; // Toyata A
                    }else // Essance
                    {
                        $vehicule = 3; // Ford A
                    }
                }
                $sqlCarburant = $sqlCarburant =="" ? "" : $sqlCarburant.=",";
                $sqlCarburant.= "(".$id.",".$mission.",".$vehicule.",".$carburant.",".$justification.",0,".$recs["mon_depense"].",'".$recs['date_depense']."','".addslashes($recs["obs_depense"])."')";
                $id++;
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }else  // Si depense non carburant enregistre dans DepenseMission
            { 
                $sqlDepense = $sqlDepense =="" ? "" : $sqlDepense.=",";
                $sqlDepense.=" (".$id2.",".$recs['code_depense'].",".$mission.",".$justification.",".$recs["mon_depense"].",'".$recs['date_depense']."','".addslashes($recs['obs_depense'])."')";
                $id2++;
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
        }
        $sqlCarburant = "INSERT INTO carburant_mission (id,mission_id,vehicule_id,carburant_id,justification_depense_id,kms,montant,date,obs) VALUE " .$sqlCarburant.";";
        $reqd = $dbd->prepare($sqlCarburant);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $sqlDepense ="INSERT INTO depense_mission (id,depense_id,mission_id,justification_depense_id,montant,dateDep,obs) VALUE ".$sqlDepense.";";
        $reqd = $dbd->prepare($sqlDepense);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        // Correction du carburant pour vehicle
        // Essance pour Ford A et Gas-oil pour Toyata A
        $reqd = $dbd->prepare("UPDATE  carburant_mission SET vehicule_id = 3  WHERE vehicule_id in(1,2,6,7,8,9) and carburant_id in(1,3)");
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $reqd = $dbd->prepare("UPDATE  carburant_mission SET vehicule_id = 1  WHERE vehicule_id in(3,4,5) and carburant_id = 2 ");
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        // Suppression Gas-oil, Essance et Sans-plan dans l'entity dépense */
        $reqd = $dbd->prepare("DELETE  FROM depense WHERE id in(2,3,4)");
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Frais Mission ***************************************************************************************
        $output->write('<info>' . str_pad('Frais Mission',35,'.'). ': </info>');
        $id = 1;
        $reqs = $dbs->prepare("SELECT * FROM frais_mission Order By id_frais_mission ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $id=1;
        $sql = "";
        foreach ($ress as $recs)
        {   
            // Mission
            $sql1 = "SELECT * FROM mission WHERE code = '".$recs["code_mission"]."'";
            $reqs1 = $dbd->prepare($sql1);
            if (!$reqs1->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
            }
            $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
            if ($resc1 !== false)
            {
                $mission = $resc1["id"];
            } 
             
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$id.",".$mission.",".$recs['code_employe'].",'".$recs['date_frais_mission']."',".$recs['mon_frais_mission'].")";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
            $id++;

        }

        $sql ="INSERT INTO frais_mission (id,mission_id,user_id,dateFm,montant) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Intervention user *****************************************************************************************
        $output->write('<info>' . str_pad('Intervention User',35,'.'). ': </info>');
        $id = 1;
        $reqs = $dbs->prepare("SELECT * FROM realisateur_intervention ");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $id=1;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$id.",".$recs['code_intervention'].",".$recs['code_employe'].")";
            $id++;
            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }
        

        $sql ="INSERT INTO intervention_user (id,intervention_id,user_id) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Fonction ***************************************************************************************************
        $output->write('<info>' . str_pad('Fonction',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM fonction Order By code_fonction ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs['code_fonction'].",'".addslashes($recs['des_fonction'])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO fonction (id,nom) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Fonction User ********************************************************************************************
        $output->write('<info>' . str_pad('Fonction User',35,'.'). ': </info>');
        $id =1;
        $reqs = $dbs->prepare("SELECT * FROM fonction_employe Order By date_fonction ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $id=1;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$id.",".$recs['code_employe'].",".$recs['code_fonction'].",'".$recs['date_fonction']."')";
            $id++;
            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO fonction_user (id,user_id,fonction_id,datefonction) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Recrutement ***************************************************************************************************************************
        $output->write('<info>' . str_pad('Recrutement',35,'.'). ': </info>');
        $id =1;
        $reqs = $dbs->prepare("SELECT * FROM recrutement Order By date_recrutement ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $id=1;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$id.",".$recs['code_employe'].",'".$recs['date_recrutement']."')";
            $id++;
            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO recrutement (id,user_id,recruter) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Pointage ***************************************************************************************************************************
        $output->write('<info>' . str_pad('Pointage',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM pointage_designation Order By des_pointageID ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$recs['des_pointageID'].",'".addslashes($recs['des_pointage'])."')";

            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO pointage (id,designation) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        // Poinatge Employe  ***************************************************************************************************************************
        $output->write('<info>' . str_pad('Pointage Employe',35,'.'). ': </info>');
        $reqs = $dbs->prepare("SELECT * FROM pointage Order By date_pointage ASC");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $nbr = count($ress);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;
        $k=0;
        $id=1;
        $sql = "";
        foreach ($ress as $recs)
        {   
            $sql = $sql =="" ? "" : $sql.=",";
            $sql .= "(" .$id.",".$recs['code_employe'].",".$recs['des_pointageID'].",'".$recs['date_pointage']."',".$recs['h_travail'].",".$recs['h_route'].",".$recs['h_sup'].",'".addslashes($recs['obs_pointage'])."')";
            $id++;
            if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                $k++;
            }else{
                $c++;
            }
        }

        $sql ="INSERT INTO pointage_user (id,user_id,pointage_id,date,hTravail,hRoute,hSup,obs) VALUES " . $sql .";";
        $reqd = $dbd->prepare($sql);
        if (!$reqd->execute()) {
            $output->writeln('');
            $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
        } 
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');
        
        //#############################################################################################################"
        // Add Auto Incrementation au fichier Entity
        //#############################################################################################################"
        
        $output->write('<info>' . str_pad('Add Auto Incrimentation',35,'.') . ': </info>');
        $find='ORM\GeneratedValue';
        $replace='@ORM\GeneratedValue';
        //$findId='ORM\Id';
        //$replaceId='@ORM\Id';

        $scandir = scandir($dirEntity);
        foreach($scandir as $fichier){
            if(substr(strtolower($fichier),-4,4)==".php"){
                $file = $dirEntity.$fichier;
                $str = file_get_contents($file);
                $str = str_replace($find, $replace, $str);
                //$str = str_replace($findId, $replaceId, $str);
                file_put_contents($file, $str);
                //$output->writeln($file."==> Remove GeratedValue" );
                //$output->writeln('<info>' . str_pad($fichier,35,'.').'</info> <comment> : Add GeneratedValue</comment>' );
            }
        }
        $output->writeln('<comment>'.str_pad('#',50,'#').' 100%.</comment>');

        //#############################################################################################################"
        // DROP FOREIGN KEY
        //#############################################################################################################"
     
        $output->write('<info>' . str_pad('DROP FOREIGN KEY',35,'.') . ': </info>');
        $sql='  SELECT TABLE_NAME,CONSTRAINT_NAME
        FROM   INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE  REFERENCED_TABLE_SCHEMA = "rtie3.4";';
        $q = $dbd->prepare($sql);
        $q->execute();
        $tables = $q->fetchAll();
        $k=0;
        foreach ($tables as $table) {
            $sql = 'ALTER TABLE ' . $table['TABLE_NAME'] . ' DROP FOREIGN KEY ' . $table['CONSTRAINT_NAME'];
            //dump($sql);
            $reqd = $dbd->prepare($sql);
            if (!$reqd->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
            }else{
                $output->write('<comment>#</comment>');   
                $k++; 
            }
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>');  
        
        //#############################################################################################################"
        // Schema Update , ADD CONSTRAINT puis CHANGE ID
        //#############################################################################################################"
            
        $output->write('<info>' . str_pad('MAJ Contrainte et Auto Inc.',35,'.') . ': </info>');
        $command = $this->getApplication()->find('doctrine:schema:update');
        $arguments = [
            'command'       => 'doctrine:schema:update',
            '--dump-sql'    => true,            
        ];
        $greetInput = new ArrayInput($arguments);
        $outputBuffer = new BufferedOutput();
        $returnCode = $command->run($greetInput, $outputBuffer);
        $content = $outputBuffer->fetch();
        //dump($content);
        $chaines = explode("\n",$content);
        $constraints = array();
        $auto_incs = array();
        $k=0;
        foreach ($chaines as $sql) 
        {
            if (strpos($sql,'ADD CONSTRAINT') !== false) 
            {
                $constraints [] = $sql;
            }elseif (strpos($sql,'CHANGE') !== false){
                $auto_incs [] = $sql;
            }
        }
        $k=0;
        $nbr = count($auto_incs) + count($constraints);
        $pas = ceil($nbr / 50);
        if ($pas == 0 ){
            $pas =1;
        }
        $c = 1;

        foreach ($auto_incs as $sql) 
        {
            $reqd = $dbd->prepare($sql);
            if (!$reqd->execute()) 
            {
                $output->writeln('');
                $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
            }else
            {
                if ($c == $pas )
                {
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else
                {
                    $c++;
                }  
            }
        }
        foreach ($constraints as $sql) 
        {
            $reqd = $dbd->prepare($sql);
            if (!$reqd->execute()) 
            {
                $output->writeln('');
                $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
            }else
            {
                if ($c == $pas )
                {
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else
                {
                    $c++;
                }   
            }
        }
        $output->writeln('<comment>'.str_pad('#',50-$k,'#').' 100%.</comment>'); 
        $output->writeln('');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',50). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' Fin de traitemet',50). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',50). '</>');
        $output->writeln('');
        //return Command::SUCCESS;
    
    }
}
