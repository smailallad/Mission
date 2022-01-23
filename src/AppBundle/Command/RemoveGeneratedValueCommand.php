<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Security\Core\Role\Role;

class RemoveGeneratedValueCommand extends ContainerAwareCommand
{  // php bin/console removeGeneratedValue
    
    protected function configure()
    {
        $this
            ->setName('rgv')
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
        $output->writeln('****** Début de Traitement Remove Generatedvalue *****');
        $output->writeln('********************************');
        $output->writeln('');

        //$file = "/var/www/html/rtie3.4/src/AppBundle/Entity/Marque.php~";
        $dir = "/var/www/html/rtie3.4/src/AppBundle/Entity/";
        
        $find='@ORM\GeneratedValue';
        $replace='ORM\GeneratedValue';

        //$find='ORM\GeneratedValue';
        //$replace='@ORM\GeneratedValue';

        $scandir = scandir($dir);
        foreach($scandir as $fichier){
            if(substr(strtolower($fichier),-4,4)==".php"){
                $file = $dir.$fichier;
                $str = file_get_contents($file);
                $str = str_replace($find, $replace, $str);
                file_put_contents($file, $str);
                $output->writeln($file."==> Remove GeratedValue" );
                //$output->writeln($file."==> Add GeratedValue" );
            }
        }

        $output->writeln('********************************');
        $output->writeln('****** Fin de Traitement Remove Generatedvalue *******');
        $output->writeln('********************************');
        $output->writeln('');

        $output->writeln('<comment>===>  Excuter php bin/console doctrine:schema:update --dump-sql');
        $output->writeln('<comment>===>  Excuter php bin/console doctrine:schema:update --force');
        $output->writeln('<comment>===>  Executer la commande : php bin/console restore <comment>');
        
    }
}
