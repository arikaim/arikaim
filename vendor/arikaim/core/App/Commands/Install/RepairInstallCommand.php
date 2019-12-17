<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Install;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\App\Install;

/**
 * Repair (reinstall) command class
 */
class RepairInstallCommand extends ConsoleCommand
{  
    /**
     * Command config
     * @return void
     */
    protected function configure()
    {
        $this->setName('install:repair')->setDescription('Arikaim CMS Repair Installation');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {
        $this->showTitle('Arikaim CMS Repair Installation');
      
        $install = new Install();
        $result = $install->install();   

        if ($result == true) {
            $this->showCompleted();  
        } else {
            $this->showError('Error repair installation');
        }
    }
}
