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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Help command class
 */
class HelpCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: help
     * @return void
     */
    protected function configure(): void 
    {
        $this->setName('help')->setDescription('Arikaim Cli Help');
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

        $command = $this->getApplication()->find('list');
        $command->run($input, $output);

        $this->showCompleted();       
    }
}
