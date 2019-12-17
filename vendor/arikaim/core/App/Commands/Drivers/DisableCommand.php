<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Drivers;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Arikaim;

/**
 * Disable driver command
 */
class DisableCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('drivers:disable')->setDescription('Disable driver');
        $this->addOptionalArgument('name','Driver name');
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
            $this->showError("Driver name required!");
            return;
        }
    
        $result = Arikaim::driver()->disable($name);
        if ($result == false) {
            $this->showError("Driver $name not exists!");
            return;
        }

        $this->showCompleted();
    }
}
