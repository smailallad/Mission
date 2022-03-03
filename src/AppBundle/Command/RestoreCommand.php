<?php
namespace AppBundle\Command;
use PDO;
use DateTime;
use AppBundle\Entity\Site;
use AppBundle\Entity\User;
use AppBundle\Entity\Zone;
use AppBundle\Entity\Roles;
use AppBundle\Entity\Client;
use AppBundle\Entity\Marque;
use AppBundle\Entity\Projet;
use AppBundle\Entity\Wilaya;
use AppBundle\Entity\Depense;
use AppBundle\Entity\Groupes;
use AppBundle\Entity\Mission;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Pointage;
use AppBundle\Entity\Vehicule;
use AppBundle\Entity\Carburant;
use AppBundle\Entity\Prestation;
use AppBundle\Entity\Recrutement;
use AppBundle\Entity\PrestationBc;
use AppBundle\Entity\FonctionUser;
use AppBundle\Entity\FraisMission;
use AppBundle\Entity\Intervention;
use AppBundle\Entity\PointageUser;
use AppBundle\Entity\DepenseMission;
use AppBundle\Entity\FamilleDepense;
use AppBundle\Entity\CarburantMission;
use AppBundle\Entity\InterventionUser;
use AppBundle\Entity\EntretienVehicule;
use AppBundle\Entity\InterventionVehicule;
use AppBundle\Entity\JustificationDepense;
use AppBundle\Entity\InterventionEntretien;
use Symfony\Component\Console\Helper\Table;
use AppBundle\Entity\KmsInterventionVehicule;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\NullOutput;

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
        //$output->writeln('<info>info</info>');
        // white text on a red background
        //$output->writeln('<error>error</error>');
        $argument = $input->getArgument('argument');
        
        if ($input->getOption('option')) {
            // ...
        }
        $dbs = new \PDO("mysql:host=localhost;dbname=dbrtie;charset=utf8", "smil", "ads2160396");
        $dbd = new \PDO("mysql:host=localhost;dbname=rtie3.4;charset=utf8", "smil", "ads2160396");
        $dbv = new \PDO("mysql:host=localhost;dbname=dbvehicule;charset=utf8", "smil", "ads2160396");
        $manager = $this->getContainer()->get('doctrine')->getManager();
        $repository = $this->getContainer()->get('doctrine');
        $encoder = $this->getContainer()->get('security.password_encoder');
        $nullOutput = new NullOutput();
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
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' Début de traitemet',101). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
        $output->writeln('');
        //$output->writeln('<comment>===>  Executer la commande : php bin/console restore <comment>');
       
        //goto fin;
        //goto test;
        
        //#############################################################################################################"
        // Vider DB dbrtie3.4 
        //#############################################################################################################"
        {
            $output->write('<info>' . str_pad('Vider DB dbrtie3.4',30,'.') . ': </info>');
            $dbd->prepare('SET foreign_key_checks = 0')->execute();
            $sql = 'SHOW TABLES';
            $reqd = $dbd->prepare($sql);
            $reqd->execute();
            $recd = $reqd->fetchAll();
            $k=1;
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
        }
        //#############################################################################################################"
        // Suppresion DB dbrtie
        //#############################################################################################################"
        {
            $output->write('<info>' . str_pad('Suppresion DB dbrtie',30,'.') . ': </info>');
            $dbs->prepare('SET foreign_key_checks = 0')->execute();
            $sql = 'SHOW TABLES';
            $reqs = $dbs->prepare($sql);
            $reqs->execute();
            $recs = $reqs->fetchAll();
            $k=1;
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
        }
        //#############################################################################################################"
        // Copier DB dbrtie_backup vers dbrtie
        //#############################################################################################################"
        {
            $output->write('<info>' . str_pad('Copier DB dbrtie',30,'.') . ': </info>');
            shell_exec("mysqldump --user=smil --password=ads2160396 dbrtie_backup | mysql --user=smil --password=ads2160396 dbrtie");
            $output->writeln('<comment>'.str_pad(' ',32). str_pad('#',100,'#').' 100%.</comment>');  
        }
        //#############################################################################################################"
        // Remove Auto Incrementation dans les Entity
        //#############################################################################################################"
        {
            $output->writeln('<info>' . str_pad('Remove Auto Incrimentation',30,'.') . ': </info>');
            $dir = "/var/www/html/rtie3.4/src/AppBundle/Entity/";
            $find='@ORM\GeneratedValue';
            $replace='ORM\GeneratedValue';
            $scandir = scandir($dir);
            foreach($scandir as $fichier){
                if(substr(strtolower($fichier),-4,4)==".php"){
                    $file = $dir.$fichier;
                    $str = file_get_contents($file);
                    $str = str_replace($find, $replace, $str);
                    file_put_contents($file, $str);
                    $output->writeln('<info>' . str_pad($fichier,30,'.').'</info> <comment> : Remove GeneratedValue</comment>' );
                    //$output->writeln($file."==> Add GeratedValue" );
                }
            }
        }
        //#############################################################################################################"
        // Generate et dump entities 
        //#############################################################################################################"
        {
            // doctrine:generate:entities
            $output->writeln('<info>' . str_pad('Generate entities',30,'.') . ': </info>');
            $command = $this->getApplication()->find('doctrine:generate:entities');
            $arguments = [
                'command'   => 'doctrine:generate:entities',
                'name'      => 'AppBundle',
            ];
            $output->writeln('<info>' . str_pad('doctrine:schema:update --dump-sql',30,'.') . ': </info>');
            $greetInput = new ArrayInput($arguments);
            $outputBuffer = new BufferedOutput();
            $returnCode = $command->run($greetInput, $outputBuffer);
            $command = $this->getApplication()->find('doctrine:schema:update');
            $arguments = [
                'command'       => 'doctrine:schema:update',
                //'name'        => 'AppBundle',
                '--dump-sql'    =>true,
            ];
            $greetInput = new ArrayInput($arguments);
            $returnCode = $command->run($greetInput, $outputBuffer);
            $output->writeln('<info>' . str_pad('doctrine:schema:update --force',30,'.') . ': </info>');
            $greetInput = new ArrayInput($arguments);
            $returnCode = $command->run($greetInput, $nullOutput);
            $command = $this->getApplication()->find('doctrine:schema:update');
            $arguments = [
                'command'       => 'doctrine:schema:update',
                //'name'        => 'AppBundle',
                '--force'       =>true,
            ];
            $greetInput = new ArrayInput($arguments);
            $returnCode = $command->run($greetInput, $outputBuffer);
        }
        //#############################################################################################################"
        // DROP FOREIGN KEY
        //#############################################################################################################"
        {
            $output->write('<info>' . str_pad('Execution :  DROP FOREIGN KEY',30,'.') . ': </info>');
            $sql='  SELECT TABLE_NAME,CONSTRAINT_NAME
            FROM   INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE  REFERENCED_TABLE_SCHEMA = "rtie3.4";';
            $q = $dbd->prepare($sql);
            $q->execute();
            $tables = $q->fetchAll();
            $k=1;
            foreach ($tables as $table) {
                //$dbd->prepare('ALTER TABLE ' . $table['TABLE_NAME'] . ' DROP FOREIGN KEY ' . $table['CONSTRAINT_NAME'])->execute();
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>'); 
        }
        //#############################################################################################################"
        // Mise a jour de l'ancienne BD
        //#############################################################################################################"
        {
            // Table Projet
            $output->write('<info>' . str_pad('MAJ table Projet',30,'.') . ': </info>');
            $sql = "UPDATE projet SET des_projet = 'OTA' WHERE code_projet = 1;
                    UPDATE projet SET des_projet = 'NOKIA' WHERE code_projet = 2;
                    UPDATE projet SET des_projet = 'CITAL' WHERE code_projet = 3;
                    UPDATE projet SET des_projet = 'SONATRACH' WHERE code_projet = 5;
                    UPDATE projet SET des_projet = 'INFRAFER' WHERE code_projet = 7;
                    UPDATE projet SET des_projet = 'OOREDOO' WHERE code_projet = 9;
                    UPDATE projet SET des_projet = 'AT' WHERE code_projet = 12;
                    UPDATE projet SET des_projet = 'MOBILIS' WHERE code_projet = 14;
                    INSERT INTO projet (code_projet, des_projet) VALUES (40,'NOKIA');
                    ";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // Table Sous Projet
            $output->write('<info>' . str_pad('MAJ table Sous Projet',30,'.') . ': </info>');
            // Renomé les noms des sous projets
            $sql = "UPDATE sous_projet SET des_sous_projet = 'DWDM AT' WHERE code_sous_projet = 24;
                    UPDATE sous_projet SET des_sous_projet = 'DWDM MOBLIS' WHERE code_sous_projet = 27;
                    UPDATE sous_projet SET des_sous_projet = 'DWDM OOREDOO' WHERE code_sous_projet = 21;
                    UPDATE sous_projet SET des_sous_projet = 'DWDM OTA' WHERE code_sous_projet = 10;
                    UPDATE sous_projet SET des_sous_projet = 'FTTH AT' WHERE code_sous_projet = 32;
                    UPDATE sous_projet SET des_sous_projet = 'MED CABLE ASN' WHERE code_sous_projet = 34;
                    UPDATE sous_projet SET des_sous_projet = 'PDH NEC' WHERE code_sous_projet = 25;
                    UPDATE sous_projet SET des_sous_projet = 'PSAX OTA' WHERE code_sous_projet = 29;
                    UPDATE sous_projet SET des_sous_projet = 'RADIO OTA' WHERE code_sous_projet = 35;
                    UPDATE sous_projet SET des_sous_projet = 'ROUTEUR OTA' WHERE code_sous_projet = 19;
                    UPDATE sous_projet SET des_sous_projet = 'SDH NEC' WHERE code_sous_projet = 26;
                    UPDATE sous_projet SET des_sous_projet = 'SDH OOREDOO' WHERE code_sous_projet = 3;
                    UPDATE sous_projet SET des_sous_projet = 'SDH OTA' WHERE code_sous_projet = 1;
                    UPDATE sous_projet SET des_sous_projet = 'SSU OTA' WHERE code_sous_projet = 28;
                    UPDATE sous_projet SET des_sous_projet = 'WERVEUR OTA' WHERE code_sous_projet = 20;
                    UPDATE sous_projet SET des_sous_projet = 'SERVEUR OOREDOO' WHERE code_sous_projet = 22;
                    UPDATE sous_projet SET des_sous_projet = 'TRAMWAY' WHERE code_sous_projet = 23;
                    UPDATE sous_projet SET des_sous_projet = 'SNC LAVALIN' WHERE code_sous_projet = 33;
                    ";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad('#',100,'#').' 100%.</comment>');    
            }
            // reaffecté les sous projet
            $output->write('<info>' . str_pad('Reafecter Sous Projet',30,'.') . ': </info>');
            $sql = "UPDATE sous_projet SET code_projet = 12 WHERE code_projet = 16;
                    UPDATE sous_projet SET code_projet = 3 WHERE code_projet = 11;
                    UPDATE sous_projet SET code_projet = 12 WHERE code_projet = 16;
                    UPDATE sous_projet SET code_projet = 9 WHERE code_projet = 10;
                    UPDATE sous_projet SET code_projet = 9 WHERE code_projet = 13;
                    UPDATE sous_projet SET code_projet = 1 WHERE code_projet = 4;
                    UPDATE sous_projet SET code_projet = 1 WHERE code_projet = 8;
                    UPDATE sous_projet SET code_projet = 1 WHERE code_projet = 15;
                    UPDATE sous_projet SET code_projet = 1 WHERE code_projet = 18;
                    UPDATE sous_projet SET code_projet = 5 WHERE code_projet = 6;
                    ";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // suppression les projets non associer
            $output->write('<info>' . str_pad('Correction anomalies',30,'.') . ': </info>');
            $sql = "DELETE FROM projet WHERE projet.code_projet NOT IN (SELECT sous_projet.code_projet FROM sous_projet);";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // Ajouter Projet SONATRACH et NOKIA pour les sites
            $sql = "INSERT INTO projet (code_projet, des_projet) VALUES (40,'NOKIA');
                    INSERT INTO projet (code_projet, des_projet) VALUES (5,'SONATRACH');
                    ";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            // Supprimer les tarif prestaions
            $sql = "DELETE FROM tarif_prestation";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            // Supprimer les prestations non utilisées
            $sql = "DELETE FROM prestation WHERE prestation.code_prestation NOT IN (	SELECT intervention.code_prestation FROM intervention);";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            // Suppression intervention N° : 3176 qui est en plus
            $sql = "DELETE FROM realisateur_intervention WHERE code_intervention =3176;
                    DELETE FROM intervention WHERE intervention.code_intervention = 3176;";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            // mise a jour table prestation 
            // Corrections des champs.
            $sql = "UPDATE prestation SET des_prestation = REPLACE(des_prestation, 'aprés', 'après') WHERE des_prestation LIKE '%aprés%'";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            $sql = "UPDATE prestation SET des_prestation = REPLACE(des_prestation, '.', '') WHERE des_prestation LIKE '%.'";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            $sql = "UPDATE prestation SET des_prestation = 'Equipement Installation Add/remove carte et module optique avec tirage fibre by night TSS320H' WHERE code_prestation = 193";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            $sql = "UPDATE prestation SET des_prestation = 'Equipement Installation Add/remove carte et module optique avec tirage fibre by night TSS160' WHERE code_prestation = 104";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad(' ',32).str_pad('#',100,'#').' 100%.</comment>');
            }
            $output->write('<info>' . str_pad('MAJ table Prestation',30,'.') . ': </info>');
            $sous_projets=[2,4,5,6,7,18]; // changer le sous projet vers OTA = 1 pour les prestations de ses sous projet
            $k=1;
            foreach ($sous_projets as $sous_projet) {
                $sql = "SELECT * FROM prestation WHERE code_sous_projet = " . $sous_projet . ";";
                $reqs = $dbs->prepare($sql);
                if (!$reqs->execute()) {
                    $output->writeln('');
                    $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
                }
                $recs = $reqs->fetchAll();
                foreach ($recs as $rec){
                    //chercher les prestations du sous projet OTA = 1
                    $sql = "SELECT * FROM prestation WHERE des_prestation = '" . addslashes($rec["des_prestation"]) . "' AND code_sous_projet = 1;";
                    $reqs1 = $dbs->prepare($sql);
                    if (!$reqs1->execute()) {
                        $output->writeln('');
                        $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
                    }
                    $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
                    if ($resc1 == false){ //  non trover: mise a jour de prestation
                        $sql ="UPDATE prestation SET code_sous_projet = 1 WHERE code_sous_projet = " . $sous_projet . " AND des_prestation = '" . addslashes($rec["des_prestation"]) ."' ;";
                        $reqs2 = $dbs->prepare($sql);
                        if (!$reqs2->execute()) {
                            $output->writeln('');
                            $output->write('<error>' . $reqs2->errorInfo()[2] . '</error>');
                        }else{
                            if ($k < 100 ){
                                $output->write('<comment>#</comment>');
                                $k++;
                            }
                        }
                    }else { //($recs["code_prestation"] != $resc1["code_prestation"]) { //   Trouver: mise a jour des interventions 
                        $sql ="UPDATE intervention SET code_prestation= " . $resc1["code_prestation"] ." WHERE code_prestation =" . $rec["code_prestation"]. ";";
                        $reqs2 = $dbs->prepare($sql);                        
                        if (!$reqs2->execute()) {
                            $output->writeln('');
                            $output->write('<error>' . $reqs2->errorInfo()[2] . '</error>');
                        }else{
                            if ($k < 100 ){
                                $output->write('<comment>#</comment>');
                                $k++;
                            }
                        }
                    }
                } 
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>'); 
            // Supprimer les prestations non utilisées
            $output->write('<info>' . str_pad('Prestations non utilisés',31,'.') . ': </info>');
            $sql = "DELETE FROM prestation WHERE prestation.code_prestation NOT IN (SELECT intervention.code_prestation FROM intervention);";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{
                $output->writeln('<comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // supprimer les sous projet non utilisé
            $output->write('<info>' . str_pad('Sous Projet non utilisés',31,'.') . ': </info>');
            $sql ="DELETE FROM sous_projet WHERE sous_projet.code_sous_projet NOT IN (SELECT prestation.code_sous_projet FROM prestation);";
            $reqs = $dbs->prepare($sql);
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else {
                $output->writeln('<comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
        }
        //#############################################################################################################"
        // Upgrade
        //#############################################################################################################"
        {
            // Client ***************************************************************************************************************
            // copier table Projet dans Entity Client
            $output->writeln('');
            $output->write('<info>' .str_pad('Copier Client',30,'.') .': </info>');
            $reqs = $dbs->prepare("SELECT * FROM projet Order By code_projet ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $k=1;
            foreach ($ress as $recs)
            {
                $client    = new Client();
                $client ->setId($recs["code_projet"])
                        ->setNom($recs["des_projet"])
                        ;
                $manager->persist($client);
                $output->write('<comment>#</comment>');
                $k++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%</comment>');
            // ******************
            // Table Site
            // ******************
            $output->writeln('<info>' .str_pad('MAJ Site',30,'.') .': </info>');
            $reqs = $dbs->prepare("ALTER TABLE site DROP client");
            if (!$reqs->execute()) {
                //$output->writeln('');
                //$output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }
            $reqs = $dbs->prepare("ALTER TABLE site ADD client int");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }
            $reqs = $dbs->prepare("UPDATE site SET client = 0 WHERE client IS NULL");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }
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
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site OTA',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // Mobilis
            $reqs = $dbs->prepare("UPDATE site SET client = 14
                                    WHERE (code_site REGEXP '^[0-9]+$' or code_site = 'PY' ) 
                                    or (code_site in ('Hydra mobilis'))
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site Mobilis',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // AT
            $reqs = $dbs->prepare("UPDATE site SET client = 12
                                    WHERE ((code_site  REGEXP '^ct|^ca|^sp[0-9]+$')
                                    or(code_site in ('SR ADL / HONET OBN C','TCC11','WH AT','Hydra mobilis','chaiba','Constantine NMS','DDO','Draa el mizane','el kala','EMRT Blida','HOUCINE DEY','WH AT','rep03','rep06')))
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site AT',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // Ooredoo ********************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 9
                                    WHERE ((code_site  REGEXP '^al[0-9]+$|^alt[0-9]+$|^ans[0-9]+$|^bas[0-9]+$|^to[0-9]+$|^tp[0-9]+$|^ts[0-9]+$|^bat[0-9]+$|^sos[0-9]+$|^se[0-9]+$|^set[0-9]+$|^sks[0-9]+$|^ai[0-9]+$|^bj[0-9]+$|^bjt[0-9]+$|^bl[0-9]+$|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob|^t[osp]t[0-9]+$')
                                    or (code_site in ('Boutique Orredoo','Boutique orredoo bej','WH_WTA','WH WTA Oran','WH WTA Rouiba','A1679','BLT02','MIS21','S0S29')))
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site OOREDOO',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // CITAL ***********************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 3
                                    WHERE ((code_site  REGEXP '^p[0-9]+$|^sst[0-9]+$')
                                    or (code_site in ('LTPCC','LSI du depot','Annaba Cclt','Téléphérique','Dépôt BEK')))
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site CITAL',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }      
            // SONATRACH ************************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 5
                                    WHERE ((code_site  REGEXP 'elr1|^pc[0-9]+$|ps[0-9]+$|sta[0-9]+$') 
                                    or (code_site in ('PY','ZCINA','Arzew','CAROTHEQUECINA','CIS','AOP','CINA','TD-HMD','24 FEV','E1C','E2A','O2P','OMN77','OMO13','OMP53','s1a','TA-GR1','U25BIS','U26LR1')))
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site SONATRACH',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // SNC LAVALIN *****************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 17
                                    WHERE (code_site = 'centrale electrique' ) 
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site SNC LAVALIN',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // NOKIA *****************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 40
                                    WHERE (code_site in ('Alcatel','WH_ALU') ) 
                                    and ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Site NOKIA',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // Le reste OTA *****************************************************************
            $reqs = $dbs->prepare("UPDATE site SET client = 1
                                    WHERE ( client =0 )
                                    ");
            if (!$reqs->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs->errorInfo()[2] . '</error>');
            }else{        
                $output->writeln('<info>'.str_pad('Le reste Site OTA',29,'.') . ' : </info><comment>'.str_pad('#',100,'#').' 100%.</comment>');
            }
            // **********************
            // Roles **********************************************************************************************************
            $output->write('<info>' . str_pad('Copier Roles',30,'.'). ': </info>');
            $k=1;
            $roles = new Roles();
            $roles
                    ->setId(1) 
                    ->setRolename('ROLE_ADMIN')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(2) 
                    ->setRolename('ROLE_GERANT')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(3) 
                    ->setRolename('ROLE_FACTURATION')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(4) 
                    ->setRolename('ROLE_SUPER_COMPTABLE')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(5) 
                    ->setRolename('ROLE_COMPTABLE')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(6) 
                    ->setRolename('ROLE_ADMINISTRATION')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(7) 
                    ->setRolename('ROLE_ROLLOUT')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(8) 
                    ->setRolename('ROLE_BUREAU')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(9) 
                    ->setRolename('ROLE_CHEF_PARK')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            $roles = new Roles();
            $roles 
                    ->setId(10) 
                    ->setRolename('ROLE_USER')
                    ;
            $manager->persist($roles);
            $output->write('<comment>#</comment>');
            $k++;
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Groupes ***************************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Groupe',30,'.'). ': </info>');
            $k=1;
            $groupes = new Groupes();
            $groupes
                    ->setId(1) 
                    ->setGroupname('ADMIN')
                    ->setRoles(['ROLE_ADMIN'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(2) 
                    ->setGroupname('GERANT')
                    ->setRoles(['ROLE_GERANT'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(3) 
                    ->setGroupname('FACTURATION')
                    ->setRoles(['ROLE_FACTURATION'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(4) 
                    ->setGroupname('SUPER_COMPTABLE')
                    ->setRoles(['ROLE_SUPER_COMPTABLE'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(5) 
                    ->setGroupname('COMPTABLE')
                    ->setRoles(['ROLE_COMPTABLE'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;   
            $groupes = new Groupes();
            $groupes 
                    ->setId(6) 
                    ->setGroupname('ADMINISTRATION')
                    ->setRoles(['ROLE_ADMINISTRATION'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(7) 
                    ->setGroupname('ROLLOUT')
                    ->setRoles(['ROLE_ROLLOUT'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(8) 
                    ->setGroupname('BUREAU')
                    ->setRoles(['ROLE_BUREAU'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(9) 
                    ->setGroupname('CHEF-PARK')
                    ->setRoles(['ROLE_CHEF_PARK'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            $groupes = new Groupes();
            $groupes 
                    ->setId(10) 
                    ->setGroupname('USER')
                    ->setRoles(['ROLE_USER'])
                    ;
            $manager->persist($groupes);
            $output->write('<comment>#</comment>');
            $k++;
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // User *****************************************************************************************************************
            $output->write('<info>' . str_pad('Copier User',30,'.'). ': </info>');
            $groupUser = $repository->getRepository('AppBundle:Groupes')->findOneByGroupname('USER');
            $groupAdmin = $repository->getRepository('AppBundle:Groupes')->findOneByGroupname('ADMIN');
            $reqs = $dbs->prepare("SELECT * FROM employe Order By code_employe ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
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
                        ->setMission($recs["active_mission"]);
                if ($recs["code_employe"] == 2){
                    $user->setGroupes($groupAdmin);
                }else{
                    $user->setGroupes($groupUser);
                }
                $user   ->setNaissance(new DateTime($recs["date_nais"]))
                        ->setNom($recs["nom_employe"])
                        ;
                $manager->persist($user);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $k++;
                    $c = 1;
                }else{
                    $c++;
                }
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Zone ***************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Zone',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM zone Order By zone");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $k=1;
            foreach ($ress as $recs)
            {
                $zone = new Zone();
                $zone   ->setId($recs['zone'])
                        ->setNom("Zone_".$recs['zone'])
                ;
                $manager->persist($zone);
                $output->write('<comment>#</comment>');
                $k++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Wilaya ***********************************************************************************************************
            $output->write('<info>' . str_pad('Copier Wilaya',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM wilaya Order By code_wilaya");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $k=1;
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
                $k++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }    
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Site *****************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Site',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM site Order By code_site");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $j = 1;
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   
                $wilaya = $repository->getRepository("AppBundle:Wilaya")->find($recs["code_wilaya"]);
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
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
                $j++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Mission **********************************************************************************************************
            $output->write('<info>' . str_pad('Copier Mission',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM mission Order By code_mission ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $j=1;
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   //$output->writeln($recs["code_employe"]);
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
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
                $j++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Gestion des vehicules 
            //Marque  ***********************************************************************************************************
            $output->write('<info>' . str_pad('Copier Marque',30,'.'). ': </info>');
            $reqs = $dbv->prepare("SELECT * FROM marque Order By id ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $k=1;
            foreach ($ress as $recs)
            {   $marque     = new Marque();
                $marque     ->setId($recs['id'])
                            ->setNom($recs['nom'])
                            ->setActive(true)
                            ;
                $manager->persist($marque);
                $output->write('<comment>#</comment>');
                $k++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Gestion des vehicules 
            // Vehicule ********************************************************************************************************
            $output->write('<info>' . str_pad('Copier Vehicule',30,'.'). ': </info>');
            $reqs = $dbv->prepare("SELECT * FROM vehicule Order By id");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $k=1;
            foreach ($ress as $recs)
            {   $marque = $repository->getRepository("AppBundle:Marque")->find($recs["marque_id"]);
                $reqAssurance = $dbv->prepare("SELECT * FROM assurance where vehicule_id = " . $recs["id"] . " AND dernier = 1" );
                $reqAssurance->execute();
                $assurance = $reqAssurance->fetch(PDO::FETCH_ASSOC);
                $reqCTech = $dbv->prepare("SELECT * FROM controletech where vehicule_id = " . $recs["id"] . " AND dernier = 1" );
                $reqCTech->execute();
                $controlTech = $reqCTech->fetch(PDO::FETCH_ASSOC);
                $vehicule    = new Vehicule();
                $vehicule   ->setId($recs["id"])
                            ->setNom($recs["nom"])
                            ->setMarque($marque)
                            ->setActive($recs["active"])
                            ->setMatricule($recs["matricule"])
                            ->setNbrjAlertRelever($recs["nbrjalertRelever"])
                            ->setKmsRelever($recs["kmsRelever"])
                            ->setDateRelever(new DateTime($recs["dateRelever"]))
                            ->setDebutAssurance(new DateTime($assurance["dateDebut"]))
                            ->setFinAssurance(new DateTime($assurance["dateFin"]))
                            ->setDebutControlTech(new DateTime($controlTech["dateDebut"]))
                            ->setFinControlTech(new DateTime($controlTech["dateFin"]))
                            ;
                $manager->persist($vehicule);
                $output->write('<comment>#</comment>');
                $k++;
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Gestion des vehicules 
            // InterventionVehicule *****************************************************************************************
            $output->write('<info>' . str_pad('Copier Intervention Vehicule',30,'.'). ': </info>');
            $reqs = $dbv->prepare("SELECT * FROM intervention Order By id ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $interventionVehicule   = new InterventionVehicule();
                $interventionVehicule   ->setId($recs['id'])
                                        ->setDesignation($recs['designation'])
                                        ->setUnite($recs['unite'])
                                        ;
                $manager->persist($interventionVehicule);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Gestion des vehicules 
            // Entretien Vehicule **********************************************************************************************
            $output->write('<info>' . str_pad('Copier Entretion Vehicule',30,'.'). ': </info>');
            $reqs = $dbv->prepare("SELECT * FROM entretien Order By id ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $vehicule = $repository->getRepository("AppBundle:Vehicule")->find($recs["vehicule_id"]);
                switch ($recs["chauffeur_id"]) {
                    case 1:
                        $chauffeur = 1;
                        break;
                    case 2:
                        $chauffeur = 2;
                        break;
                    case 3:
                        $chauffeur = 9;
                    case 4:
                        $chauffeur = 24;
                        break;
                    case 5:
                        $chauffeur = 5;
                        break;
                    case 6:
                        $chauffeur = 12;
                        break;
                    case 7:
                        $chauffeur = 3;
                        break;
                    case 8:
                        $chauffeur = 47;
                        break;             
                    }
                $user = $repository->getRepository("AppBundle:User")->find($chauffeur);
                $entretienVehicule  = new EntretienVehicule();
                $entretienVehicule  ->setId($recs['id'])
                            ->setVehicule($vehicule)
                            ->setUser($user)
                            ->setDate(new DateTime($recs["date"]))
                            ->setKms($recs['kms'])
                            ->setObs($recs['obs'])
                            ;
                $manager->persist($entretienVehicule);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Gestion des vehicules 
            // KmsInterventionVehicule *********************************************************************************************
            $output->write('<info>' . str_pad('Copier Kms Vehicule',30,'.'). ': </info>');
            $reqs = $dbv->prepare("SELECT * FROM kmsintervention Order By id ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $marque = $repository->getRepository("AppBundle:Marque")->find($recs["marque_id"]);
                $interventionVehicule = $repository->getRepository("AppBundle:InterventionVehicule")->find($recs["intervention_id"]);
                $kmsInterventionVehicule    = new KmsInterventionVehicule();
                $kmsInterventionVehicule    ->setId($recs['id'])
                                            ->setMarque($marque)
                                            ->setInterventionVehicule($interventionVehicule)
                                            ->setKms($recs['kms'])
                                            ->setObs($recs['obs'])
                                            ;   
                $manager->persist($kmsInterventionVehicule);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Gestion des vehicules 
            // InterventionEntretien ou LigneEntretien **********************************************************************
            $output->write('<info>' . str_pad('Copier Intervention Entretien',30,'.'). ': </info>');
            $reqs = $dbv->prepare("SELECT * FROM ligneentretien Order By id ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $entretienVehicule = $repository->getRepository("AppBundle:EntretienVehicule")->find($recs["entretien_id"]);
                $interventionVehicule = $repository->getRepository("AppBundle:InterventionVehicule")->find($recs["intervention_id"]);
                $interventionEntretien    = new InterventionEntretien();
                $interventionEntretien    ->setId($recs['id'])
                                            ->setEntretienVehicule($entretienVehicule)
                                            ->setInterventionVehicule($interventionVehicule)
                                            ->setQte($recs['qte'])
                                            ->setObs($recs['obs'])
                                            ;   
                $manager->persist($interventionEntretien);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }            
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Projet *********************************************************************************************************
            // copier table sous projet dans Entity Projet
            $output->write('<info>' . str_pad('Copier Projet',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM sous_projet Order By code_sous_projet ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {
                $client = $repository->getRepository('AppBundle:Client')->find($recs['code_projet']);
                $projet    = new Projet();
                $projet ->setId($recs["code_sous_projet"])
                        ->setNom($recs["des_sous_projet"])
                        ->setclient($client)
                        ;
                $manager->persist($projet);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            // Prestation ***************************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Prestation',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM prestation WHERE code_sous_projet");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {  
                $projet = $repository->getRepository('AppBundle:Projet')->find($recs['code_sous_projet']);
                $prestation     = new Prestation();
                $prestation     ->setId($recs["code_prestation"])
                                ->setNom($recs["des_prestation"])
                                ->setProjet($projet)
                            ;
                $manager->persist($prestation);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Intervention ***********************************************************************************************
            $output->write('<info>' . str_pad('Copier Intervention',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM intervention Order By code_intervention ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
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
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Famille depense *********************************************************************************************
            $output->write('<info>' . str_pad('Copier Famille',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM famille_dep Order By code_fam_dep ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {
                $destination    = new FamilleDepense();
                $destination    ->setId($recs["code_fam_dep"])
                                ->setNom($recs["des_fam_dep"]);
                $manager->persist($destination);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }           
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Depense *******************************************************************************************
            $output->write('<info>' . str_pad('Copier Depense',30,'.'). ': </info>');
            $reqs = $dbs->prepare("UPDATE depense SET code_depense = 48 WHERE code_depense = 87 ");
            $reqs->execute();
            $reqs = $dbs->prepare("SELECT * FROM table_depense WHERE (code_depense != 87) Order By code_depense ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $familleDepense = $repository->getRepository('AppBundle:FamilleDepense')->find($recs['code_fam_dep']);
                $destination    = new Depense();
                $destination    ->setId($recs['code_depense'])
                                ->setNom($recs["des_depense"])
                                ->setNouveau($recs['nouveau'])
                                ->setFamilleDepense($familleDepense)
                            ;
                $manager->persist($destination);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Justification Depense ****************************************************************************************
            $output->write('<info>' . str_pad('Copier Justification',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM jus_depense Order By code_jus ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   //$familleDepense = $repository->getRepository('AppBundle:FamilleDepense')->find($recs['code_fam_dep']);
                $destination    = new JustificationDepense();
                $destination    ->setId($recs["code_jus"])
                                ->setNom($recs["des_jus"])
                            ;
                $manager->persist($destination);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Depense Mission ******************************************************************************************
            $output->write('<info>' . str_pad('Copier Depense Mission',30,'.'). ': </info>');
            // Enlever Cle ID, ajout Method SetId()
            // Remplire la table Carburant créer nouvellement
            // 2 : sans plan,   3 : gas-oil,    4 : essance
            // 1 : sans plan ,  2 : gas-oil ,   3 : essance
            $id = 1;
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
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $reqs = $dbs->prepare("SELECT * FROM depense Order By id_depense ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
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
                            $vehicule = $repository->getRepository('AppBundle:Vehicule')->find(3); // Ford A
                        }
                    }
                    $carburantMission = new CarburantMission();
                    $carburantMission   ->setId($id)
                                        ->setMission($mission)
                                        ->setVehicule($vehicule)
                                        ->setCarburant($carburant)
                                        ->setJustificationDepense($justification)
                                        ->setKms(0)
                                        ->setMontant($recs["mon_depense"])
                                        ->setDate(new Datetime($recs['date_depense']))
                                        ->setObs($recs["obs_depense"])
                                ;
                    $manager->persist($carburantMission);
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
                    if ($c == $pas ){
                        $output->write('<comment>#</comment>');
                        $c = 1;
                        $k++;
                    }else{
                        $c++;
                    }
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
            //*** Essance pour Ford A et Gas-oil pour Toyata A
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
            //*** Suppression Gas-oil, Essance et Sans-plan dans l'entity dépense */
            $reqd = $dbd->prepare("DELETE  FROM depense WHERE id in(2,3,4)");
            if (!$reqd->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
            }

            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Frais Mission ***************************************************************************************
            $output->write('<info>' . str_pad('Copier Frais Mission',30,'.'). ': </info>');
            $id = 1;
            $reqs = $dbs->prepare("SELECT * FROM frais_mission Order By id_frais_mission ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $mission = $repository->getRepository('AppBundle:Mission')->findOneBy(["code" => $recs['code_mission']]);
                $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $fraisMission   = new FraisMission();
                $fraisMission   ->setId($id)
                                ->setMission($mission)
                                ->setUser($user)
                                ->setDateFm(new DateTime($recs["date_frais_mission"]))
                                ->setMontant($recs["mon_frais_mission"])
                            ;
                $manager->persist($fraisMission);
                $id++;
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Intervention user *****************************************************************************************
            $output->write('<info>' . str_pad('Copier Intervention User',30,'.'). ': </info>');
            $id = 1;
            $reqs = $dbs->prepare("SELECT * FROM realisateur_intervention ");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $intervention = $repository->getRepository('AppBundle:Intervention')->find($recs['code_intervention']);
                $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $interventionUser   = new InterventionUser();
                $interventionUser   ->setId($id)
                                    ->setIntervention($intervention)
                                    ->setUser($user)
                                    ;
                $manager->persist($interventionUser);
                $id++;
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Tarif Prestation *****************************************************************************************
            /*$output->writeln('<comment> 100%.</comment>');
            $output->write('<info>' . str_pad('Copier Tarif',30,'.'). ': </info>');
            $id = 1;
            $reqs = $dbs->prepare("DELETE  FROM tarif_prestation WHERE code_prestation NOT IN (SELECT code_prestation FROM prestation)");
            $reqs->execute();
            $reqs = $dbs->prepare("SELECT * FROM tarif_prestation Order By zone ASC, code_prestation ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            foreach ($ress as $recs)
            {   $prestation = $repository->getRepository('AppBundle:Prestation')->find($recs['code_prestation']);
                $zone = $repository->getRepository('AppBundle:Zone')->find($recs['zone']);
                $prestationBc    = new PrestationBc();
                $prestationBc    ->setId($id)
                                    ->setPrestation($prestation)
                                    ->setZone($zone)
                                    ->setMontant($recs["tarif_prestation"])
                            ;
                $manager->persist($prestationBc);
                $id++;
                if ($c == $pas ){
                $output->write('<comment>#</comment>');
                $c = 1;
                }else{
                    $c++;
                }   
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }*/
            //*** Fonction ***************************************************************************************************
            $output->write('<info>' . str_pad('Copier Fonction',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM fonction Order By code_fonction ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   
                $fonction   = new Fonction();
                $fonction   ->setId($recs["code_fonction"])
                            ->setNom($recs["des_fonction"])
                            ;
                $manager->persist($fonction);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Fonction User ********************************************************************************************
            $output->write('<info>' . str_pad('Copier Fonction User',30,'.'). ': </info>');
            $id =1;
            $reqs = $dbs->prepare("SELECT * FROM fonction_employe Order By date_fonction ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $fonction = $repository->getRepository('AppBundle:Fonction')->find($recs['code_fonction']);
                $fonctionUser   = new FonctionUser();
                $fonctionUser   ->setId($id)
                                ->setUser($user)
                                ->setFonction($fonction)
                                ->setDatefonction(new Datetime($recs['date_fonction']))
                            ;
                $manager->persist($fonctionUser);
                $id++;
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
            }
            try {
                $manager->flush();
            } catch (\Throwable $th) {
                $output->writeln('');
                $output->writeln('<error>Erreur: '. $th->getMessage() .'</error>');
                $manager = $this->getContainer()->get('doctrine')->resetManager();
            }
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Recrutement ***************************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Recrutement',30,'.'). ': </info>');
            $id =1;
            $reqs = $dbs->prepare("SELECT * FROM recrutement Order By date_recrutement ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $user = $repository->getRepository('AppBundle:User')->find($recs['code_employe']);
                $recrutement    = new Recrutement();
                $recrutement    ->setId($id)
                                ->setUser($user)
                                ->setRecruter(new Datetime($recs['date_recrutement']))
                            ;
                $manager->persist($recrutement);
                $id++;
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Pointage ***************************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Pointage',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM pointage_designation Order By des_pointageID ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
            foreach ($ress as $recs)
            {   $pointage   = new Pointage();
                $pointage   ->setId($recs['des_pointageID'])
                            ->setDesignation($recs['des_pointage'])
                            ;
                $manager->persist($pointage);
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
            //*** Poinatge Employe  ***************************************************************************************************************************
            $output->write('<info>' . str_pad('Copier Pointage Employe',30,'.'). ': </info>');
            $reqs = $dbs->prepare("SELECT * FROM pointage Order By date_pointage ASC");
            $reqs->execute();
            $ress = $reqs->fetchAll();
            $nbr = count($ress);
            $pas = ceil($nbr / 100);
            if ($pas == 0 ){
                $pas =1;
            }
            $c = 1;
            $k=1;
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
                if ($c == $pas ){
                    $output->write('<comment>#</comment>');
                    $c = 1;
                    $k++;
                }else{
                    $c++;
                }
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');
        }
        //#############################################################################################################"
        // Add Auto Incrementation au fichier Entity
        //#############################################################################################################"
        {
            $output->writeln('<info>' . str_pad('Add Auto Incrimentation',30,'.') . ': </info>');
            $dir = "/var/www/html/rtie3.4/src/AppBundle/Entity/";
            $find='ORM\GeneratedValue';
            $replace='@ORM\GeneratedValue';
            $scandir = scandir($dir);
            foreach($scandir as $fichier){
                if(substr(strtolower($fichier),-4,4)==".php"){
                    $file = $dir.$fichier;
                    $str = file_get_contents($file);
                    $str = str_replace($find, $replace, $str);
                    file_put_contents($file, $str);
                    //$output->writeln($file."==> Remove GeratedValue" );
                    $output->writeln('<info>' . str_pad($fichier,30,'.').'</info> <comment> : Add GeneratedValue</comment>' );
                }
            }
        }
        
        $msg = ' Veillez Executer la commande : ';
        $cmd = ' php bin/console update';
        $output->writeln('<fg=black;bg=cyan>' . str_pad(' ',100) .'</>');
        $output->writeln('<fg=black;bg=cyan>' .$msg .'</><bg=cyan;options=bold>'.$cmd . str_pad(' ',(100-strlen($msg)-strlen($cmd))) .'</>');
        $output->writeln('<fg=black;bg=cyan>' . str_pad(' ',100) .'</>');
        $output->writeln('');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' Fin de traitemet',100). '</>');
        $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
        $output->writeln('');
    }
}
