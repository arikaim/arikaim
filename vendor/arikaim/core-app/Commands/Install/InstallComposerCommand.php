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
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Arikaim\Core\System\Process;

/**
 * Composer update command class
 */
class InstallComposerCommand extends ConsoleCommand
{  
    /**
     * Command config  
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('composer:update')->setDescription('Update composer packages');
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
        
        Process::runComposerCommand('update',false,true);  

        $this->showCompleted();       
    }
}
