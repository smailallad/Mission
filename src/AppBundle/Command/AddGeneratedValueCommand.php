<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Security\Core\Role\Role;

class AddGeneratedValueCommand extends ContainerAwareCommand
{  // php bin/console restore
    
    protected function configure()
    {
        $this
            ->setName('addGeneratedValue')
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
        $output->writeln('****** Début de Traitement Add Generatedvalue *****');
        $output->writeln('********************************');
        $output->writeln('');

        $file = "/var/www/html/rtie3.4/src/AppBundle/Entity/Marque.php~";
        $dir = "/var/www/html/rtie3.4/src/AppBundle/Entity/";
        
        //$find='@ORM\GeneratedValue';
        //$replace='ORM\GeneratedValue';

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
                $output->writeln($file."==> Add GeratedValue" );
            }
        }

        $output->writeln('********************************');
        $output->writeln('****** Fin de Traitement Add Generatedvalue *******');
        $output->writeln('********************************');
        $output->writeln('');
    }
}
