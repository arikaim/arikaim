<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Extensions;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Disable extension
 */
class DisableCommand extends ConsoleCommand
{  
    /**
     * Configurecommand
     * name: extensions:disable [ext name]
     * @return void
     */
    protected function configure(): void 
    {
        $this->setName('extensions:disable')->setDescription('Disable extension');
        $this->addOptionalArgument('name','Extension Name');
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {       
        global $arikaim;

        $this->showTitle();

        $name = $input->getArgument('name');
        if (empty($name) == true) {           
            $this->showError('ARGUMENT_ERROR');
            return;
        }
        
        $this->writeFieldLn('Name',$name);
        
        $manager = $arikaim->get('packages')->create('extension');
        $package = $manager->createPackage($name);
        
        if ($package == false) {
            $this->showError('Extension ' . $name . ' not exists!');
            return;
        }
        $installed = $package->getProperties()->get('installed');
       
        if ($installed == false) {           
            $this->showError('EXTENSION_NOT_INSTALLED');
            return;
        }

        $result = $manager->disablePackage($name);
        
        $arikaim->get('cache')->clear();

        if ($result == false) {
            $this->showError("Can't disable extension!");
            return;
        }

        $this->showCompleted();
    }
}
