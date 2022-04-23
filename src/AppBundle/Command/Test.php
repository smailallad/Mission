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

class Test extends ContainerAwareCommand
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
            ->setName('test')
            ->setDescription('')
        ;
    }

    

    protected function execute(InputInterface $input, OutputInterface $output)
    {       
        $dbs = new \PDO("mysql:host=localhost;dbname=dbrtie;charset=utf8", "smil", "ads2160396");
        $dbd = new \PDO("mysql:host=localhost;dbname=rtie3.4;charset=utf8", "smil", "ads2160396");
        $dbv = new \PDO("mysql:host=localhost;dbname=dbvehicule;charset=utf8", "smil", "ads2160396");

        $reqs = $dbs->prepare("SELECT * FROM wilaya Order By code_wilaya");
        $reqs->execute();
        $ress = $reqs->fetchAll();
    
        foreach ($ress as $recs)
        {
            //chercher les frais de mission
            $fm = 0;
            $sql = "SELECT * FROM frais_mission_wilaya WHERE code_wilaya = " . $recs["code_wilaya"];
            $reqs1 = $dbs->prepare($sql);
            if (!$reqs1->execute()) {
                $output->writeln('');
                $output->write('<error>' . $reqs1->errorInfo()[2] . '</error>');
            }
            $resc1 = $reqs1->fetch(PDO::FETCH_ASSOC);
            if ($resc1 !== false)
            {
                $fm = $resc1["mon_f_mission"];
                $code = $recs["code_wilaya"];
                $code = str_pad($code, 2, "0", STR_PAD_LEFT); 
                $sql = "UPDATE wilaya SET code =  '" . $code . "' , montantFm = " . $fm . "  WHERE id = " . $recs["code_wilaya"];
                $reqd = $dbd->prepare($sql);
                if (!$reqd->execute()) {
                    $output->writeln('');
                    $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
                }
            } 
            dump($code . " : " . $recs["nom_wilaya"] . ": " . $fm . " : " . gettype($code));


        }

        
        
        
        
        //********************************* */
        $output->writeln('');
        $output->writeln('********************************');
        $output->writeln('****** Fin de Traitement *******');
        $output->writeln('********************************');
        $output->writeln('');
        return 0;
    }
}
