<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Session;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Http\Session;

/**
 * Clear session varibales and start new session
 */
class RestartCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name cache:clear 
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('session:restart')->setDescription('Clear session varibales and start new session');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function executeCommand($input, $output)
    {
        Session::restart();

        $this->showTitle('Restart session.');        
        $this->showCompleted();
    }
}
