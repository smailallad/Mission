<?php
namespace AppBundle\Command;
use PDO;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
class UpdateCommand extends ContainerAwareCommand
{  // php bin/console restore
    protected function configure()
    {
        $this
            ->setName('update')
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
        $output->writeln($argument);
        if ($input->getOption('option')) {
            // ...
        }
        //$dbs = new \PDO("mysql:host=localhost;dbname=dbrtie;charset=utf8", "smil", "ads2160396");
        $dbd = new \PDO("mysql:host=localhost;dbname=rtie3.4;charset=utf8", "smil", "ads2160396");
        //$dbv = new \PDO("mysql:host=localhost;dbname=dbvehicule;charset=utf8", "smil", "ads2160396");
        //$manager = $this->getContainer()->get('doctrine')->getManager();
        //$repository = $this->getContainer()->get('doctrine');
        //$encoder = $this->getContainer()->get('security.password_encoder');

        //#############################################################################################################"
        // DROP FOREIGN KEY
        //#############################################################################################################"
        {   $output->writeln('');
            $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
            $output->writeln('<bg=yellow;options=bold>' .str_pad(' Début de traitemet',101). '</>');
            $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
            $output->writeln('');
            $output->write('<info>' . str_pad('Execution :  DROP FOREIGN KEY',30,'.') . ': </info>');
            $sql='  SELECT TABLE_NAME,CONSTRAINT_NAME
            FROM   INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE  REFERENCED_TABLE_SCHEMA = "rtie3.4";';
            $q = $dbd->prepare($sql);
            $q->execute();
            $tables = $q->fetchAll();
            $k=1;
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
            $output->writeln('<comment>'.str_pad('#',100-$k,'#').' 100%.</comment>');  
        }
        
        //#############################################################################################################"
        // Schema Update , ADD CONSTRAINT puis CHANGE ID
        //#############################################################################################################"
        {
            
            $output->write('<info>' . str_pad('MAJ Contrainte et Auto Inc.',30,'.') . ': </info>');
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
            $k=1;
            foreach ($chaines as $sql) {
                if (strpos($sql,'ADD CONSTRAINT') !== false) {
                    $constraints [] = $sql;
                }elseif (strpos($sql,'CHANGE') !== false){
                    $auto_incs [] = $sql;
                }
            }
            $k=1;
            foreach ($auto_incs as $sql) {
                $reqd = $dbd->prepare($sql);
                if (!$reqd->execute()) {
                    $output->writeln('');
                    $output->write('<error>' . $reqd->errorInfo()[2] . '</error>');
                }else{
                    $output->write('<comment>#</comment>');   
                    $k++;    
                }
            }
            foreach ($constraints as $sql) {
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
            $output->writeln('');
            $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
            $output->writeln('<bg=yellow;options=bold>' .str_pad(' Fin de traitemet',100). '</>');
            $output->writeln('<bg=yellow;options=bold>' .str_pad(' ',100). '</>');
            $output->writeln('');
        }
        
    }
}
