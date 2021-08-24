<?php
namespace AppBundle\Command;
use PDO;
use AppBundle\Repository\UserRepository;
//use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Question\Question;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Udb extends ContainerAwareCommand
{  // php bin/console user
    /**
     *
     * @var EntityManagerInterface
     */
    /**
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    private $em;
    
    /**
     *
     * @var UserRepository
     */
    //private $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        //$this->userRepository = $userRepository;
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setName('udb')
            ->setDescription('')
        ;
    }

    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$io = new SymfonyStyle($input, $output);
        //$email = $input->getArgument('email');
        //$email = 'smail.allad@gmail.com';
        //$username='smail.allad';
        //$nom='Smail ALLAD';
        exit;
        $dbs = new \PDO("mysql:host=localhost;dbname=dbrtie;charset=utf8", "root", "ads2160396");
        $dbd = new \PDO("mysql:host=localhost;dbname=rtie3.4;charset=utf8", "root", "ads2160396");

        // Groupes **********************************************************************************************************************
        $output->writeln('<question>Groupe : </question>');
        $error = false;
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (1,'ADMIN','".serialize(['ROLE_ADMIN'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (2,'GERANT','".serialize(['ROLE_GERANT'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (3,'SUPER_COMPTABLE','".serialize(['ROLE_SUPER_COMPTABLE'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (4,'COMPTABLE','".serialize(['ROLE_COMPTABLE'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (5,'ADMINISTRATION','".serialize(['ROLE_ADMINISTRATION'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (6,'ROLLOUT','".serialize(['ROLE_ROLLOUT'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (7,'BUREAU','".serialize(['ROLE_BUREAU'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO groupes (id,groupname,roles)
        VALUES (8,'USER','".serialize(['ROLE_USER'])."')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$error){
            $output->writeln(" Created successfully");
        }

        // Client **********************************************************************************************************************
        $output->writeln('<question>Client : </question>');
        $error = false;
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (1,'OTA')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (2,'ATM')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (3,'AT')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (4,'Ooredoo')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (5,'CITAL')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (6,'ELR1')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        if (!$dbd->exec("INSERT INTO client (id,nom)
        VALUES (7,'SNC LAVALIN')")) {
            $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
            $error = true;
        }
        
        if (!$error){
            $output->writeln(" Created successfully");
        }

        // Zone ************************************************************************************************************************
        $output->writeln('<question>Zone : </question>');
        $reqs = $dbs->prepare("SELECT * FROM zone Order By zone");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $error = false;
        foreach ($ress as $recs) {   
            if (!$dbd->exec("INSERT INTO zone (id,nom) VALUES (".$recs['zone'].",'Zone_" . $recs['zone']."')")) {
                $output->writeln('<error>'.$dbd->errorInfo()[2].'</error>');
                $error = true;
            }else {
                $output->write('#');
            }
        }
        if (!$error){
            $output->writeln("<comment> Created successfully </comment>");
        }else{
            $output->writeln('');
        }

        // Wilaya ************************************************************************************************************************
        $output->writeln('<question>wilaya : </question>');
        $reqs = $dbs->prepare("SELECT * FROM wilaya Order By code_wilaya");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        $error = false;
        foreach ($ress as $recs) {   
            $reqd = $dbd->prepare('INSERT INTO wilaya (id,nom,zone_id,montantFm) VALUES(?,?,?,?)');
            if (!$reqd->execute(array($recs["code_wilaya"],addslashes($recs["nom_wilaya"]),$recs["zone_wilaya"],0))){
                $output->writeln('<error>'.$reqd->errorInfo()[2].'</error>');
                $error = true;
            }else {
                $output->write('#');
            }
        }
        if (!$error){
            $output->writeln("<comment> Created successfully </comment>");
        }else{
            $output->writeln('');
        }


        // Sites ************************************************************************************************************************
        $output->writeln('<question>Groupe : </question>');
        $reqs = $dbs->prepare("ALTER TABLE site ADD client SMALLINT NOT NULL DEFAULT '0'");
        $reqs->execute();
        
        // Suppression les unitiles *****************************************************************
        $reqs = $dbs->prepare("DELETE FROM `site`   WHERE   code_site = 'E1C' 
                                                    or      code_site = 'E2A' 
                                                    or      code_site = 'alcatel'
                                                    or      code_site = 'RTIE'
                                                    or      code_site = 'ZCINA'
                                                    or      code_site = 'CINA'
                                                    or      code_site = 'AOP'
                                                    or      code_site = 'Siege CFAO'
                                                    ");
        $reqs->execute();
        
        // OTA *************************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 1 WHERE (code_site  REGEXP '^[aco][0-9]{2}[sxmbt]|^[a-z \-]+$|^msc[0-9]+$' or code_site = 'WH OTA' or code site = 'WH-OTA Constantine' ) and ( client =0 )");
        $reqs->execute();

        // ATM *************************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 2 WHERE (code_site REGEXP '^[0-9]+$' or code_site = 'PY' ) and ( client =0 )");
        $reqs->execute();

        // AT **************************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 3 WHERE (code_site  REGEXP '^ct|^ca' or code_site ='Hydra mobilis' or code_site = 'WH AT' ) and ( client =0 )");
        $reqs->execute();

        // Ooredoo ********************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 4 WHERE (code_site  REGEXP '^al|^ans|^bas|^to|^tp|^ts|^bat[0-9]+|^sos[0-9]+$|^se[0-9]+$|^sks[0-9]+$|^ai[0-9]+$|^bj|^bl|^bm|^bo|^ch|^et|^gu|^kh|^ms[0-9]+|^ob' or code_site = 'Boutique Orredoo' or code_site ='Boutique orredoo bej' or code_site = 'WH WTA Oran' or code_site = 'WH WTA Rouiba' ) and ( client =0 )");
        $reqs->execute();

        // CITAL ***********************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 5 WHERE (code_site  REGEXP '^p[0-9]+$|^sst[0-9]+$' or code_site = 'LSI du depot' or code_site = 'LTPCC' ) and ( client =0 )");
        $reqs->execute();

        // ELR1 ************************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 6 WHERE (code_site  REGEXP 'elr1' ) and ( client =0 )");
        $reqs->execute();

        // SNC LAVALIN ************************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 7 WHERE (code_site = 'SNC LAVALIN' ) and ( client =0 )");
        $reqs->execute();
        

        // Le reste OTA  *****************************************************************
        $reqs = $dbs->prepare("UPDATE site SET client = 1 WHERE (client=0)");
        $reqs->execute();

        //*** Site
        $reqs = $dbs->prepare("SELECT * FROM site Order By code_site");
        $reqs->execute();
        $ress = $reqs->fetchAll();
        //foreach ($ress as $recs)
        //{
        //    $reqd = $dbd->prepare('INSERT INTO site (id,nom,wilaya_id,nouveau,client_id) VALUES(?,?,?,?,?)');
        //    $reqd->execute(array($recs["code_site"],$recs["nom_site"],$recs["code_wilaya"],$recs["nouveau"],$recs["id_client"]));
        //    $output->writel('#');
        //}

        $error = false;
        foreach ($ress as $recs) {   
            $reqd = $dbd->prepare('INSERT INTO site (code,nom,wilaya_id,nouveau,client_id) VALUES(?,?,?,?,?)');
            if (!$reqd->execute(array($recs["code_site"],addslashes($recs["nom_site"]),$recs["code_wilaya"],$recs["nouveau"],$recs["client"]))){
                $output->writeln('<error>'.$reqd->errorInfo()[2].'</error>');
                $error = true;
            }else {
                $output->write('#');
            }
        }
        if (!$error){
            $output->writeln("<comment> Created successfully </comment>");
        }else{
            $output->writeln('');
        }
        //*** Fin Site

        
        
        
        //********************************* */
        $output->writeln('');
        $output->writeln('********************************');
        $output->writeln('****** Fin de Traitement *******');
        $output->writeln('********************************');
        $output->writeln('');
        return 0;
    }
}
