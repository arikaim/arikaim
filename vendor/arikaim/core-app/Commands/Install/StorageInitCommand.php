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

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\App\Install;

/**
 * Storage init command class
 */
class StorageInitCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: storage:init
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('storage:init')->setDescription('Init storage.');
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
        $this->showTitle();
             
        $install = new Install();
        $result = $install->initStorage();
       
        if ($result == true) {
            $this->showCompleted();  
        } else {
            $this->showError('Error');  
        }
    }
}
