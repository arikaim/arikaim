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
    protected function configure(): void
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
        global $arikaim;
        
        $this->showTitle(); 
        $name = $input->getArgument('name');
        if (empty($name) == true) {          
            $this->showError('ARGUMENT_ERROR');
            return;
        }
        
        $this->writeFieldLn('Name',$name);
      
        if ($arikaim->get('driver')->has($name) == false) {
            $this->showError('DRIVER_NOT_EXISTS_ERROR');
            return;
        }
       
        $result = $arikaim->get('driver')->disable($name);
        if ($result == false) {           
            $this->showError('DRIVER_DISABLE_ERROR');           
            return;
        }

        $this->showCompleted();
    }
}
