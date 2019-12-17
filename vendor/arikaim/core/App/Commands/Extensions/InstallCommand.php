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

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Arikaim;

/**
 * Install extension command
 */
class InstallCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('extensions:install')->setDescription('Install extension');
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
        $name = $input->getArgument('name');
        if (empty($name) == true) {
            $this->showError("Extension name required!");
            return;
        }
    
        $manager = Arikaim::packages()->create('extension');
        $package = $manager->createPackage($name);
        if ($package == false) {
            $this->showError("Extension $name not exists!");
            return;
        }

        $result = $package->install();
     
        if ($result == false) {
            $this->showError("Can't install extension!");
            return;
        }

        $this->showCompleted();
    }
}
