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

use Arikaim\Core\Console\ConsoleCommand;

/**
 *  Clear Logs command
 */
class ClearLogsCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('logs:clear')->setDescription('Clear logs');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function executeCommand($input, $output)
    {
        global $arikaim;

        $this->showTitle();
    
         $arikaim->get('logger')->deleteSystemLogs();
       
        $this->showCompleted();
    }
}
