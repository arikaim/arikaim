<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Clear env vars command class
 */
class ClearEnvCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: env:clear
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('env:clear')->setDescription('Clear environment vars.');
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
        global $arikaim;

        $this->showTitle();
       
        $arikaim->get('config')->setValue('environment',[]); 
        $arikaim->get('config')->save();

        $this->showCompleted();  
    }
}
