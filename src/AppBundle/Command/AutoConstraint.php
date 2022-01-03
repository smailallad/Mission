<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Security\Core\Role\Role;

class AutoConstraint extends ContainerAwareCommand
{  // php bin/console restore
    
    protected function configure()
    {
        $this
            ->setName('autoConstraint')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $output->writeln($argument);
        if ($input->getOption('option')) {
            // ...
        } 
        //********************************* */
        //          Début Traitement
        //********************************* */

        $output->writeln('');
        $output->writeln('********************************');
        $output->writeln('****** Début de Traitement *****');
        $output->writeln('********************************');
        $output->writeln('');

        //$file = "/var/www/html/rtie3.4/src/AppBundle/Entity/Marque.php~";
        $dir = "/var/www/html/rtie3.4/src/";
        
        $find='@ORM\GeneratedValue';
        $replace='ORM\GeneratedValue';
        $file ='/var/www/html/rtie3.4/src/sql.sql';
        $auto = '/var/www/html/rtie3.4/src/auto.sql';
        $cons = '/var/www/html/rtie3.4/src/constraint.sql';
        $fauto = fopen($auto,'w+');
        $fcons = fopen($cons,'w+');
        //$find='ORM\GeneratedValue';
        //$replace='@ORM\GeneratedValue';

        

        foreach(file($file) as $line) {
            if (strpos($line,"AUTO_INCREMENT") != false){
                //$output->writeln($line);
                fwrite($fauto,$line);
            }else{
                fwrite($fcons,$line);
            }
         } 
         fclose($fauto);
         fclose($fcons);
        $output->writeln('********************************');
        $output->writeln('****** Fin de Traitement *******');
        $output->writeln('********************************');
        $output->writeln('');

        //$output->writeln('<comment>===>  Excuter php bin/console doctrine:schema:update --dump-sql');
        //$output->writeln('<comment>===>  Excuter php bin/console doctrine:schema:update --force');
        //$output->writeln('<comment>===>  Executer la commande : php bin/console restore <comment>');
        
    }
}
