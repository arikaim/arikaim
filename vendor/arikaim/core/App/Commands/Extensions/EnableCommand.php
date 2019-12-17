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
 * Enable extension command
 */
class EnableCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('extensions:enable')->setDescription('Enable extension');
        $this->addOptionalArgument('name','Extension Name');
    }

    /**
     * Execute command
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
        $installed = $package->getProperties()->get('installed');
      
        if ($installed == false) {
            $this->showError("Extension $name not installed!");
            return;
        }
        $result = $manager->enablePackage($name);
        if ($result == false) {
            $this->showError("Can't enable extension!");
            return;
        }
        $this->showCompleted();
    }
}
