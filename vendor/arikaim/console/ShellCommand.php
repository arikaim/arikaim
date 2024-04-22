<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Console;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Console shell
 */
class ShellCommand extends ConsoleCommand
{  
    /**
     * Config command
     * 
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('shell');
        $this->setDefault(true);
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
        $app = $this->getApplication();
        $this->style->section($app->getName());   

        $helper = $this->getHelper('question');
        $question = new Question('arikaim > ');
      
        $app->setAutoExit(false);
      
        while(true) {
            $command = \trim($helper->ask($input, $output, $question));
            if ($command == 'exit') { 
                $this->style->newLine();
                exit();
            }
            if (empty($command) == false) {
                $commandInput = new StringInput($command);
                $app->run($commandInput,$output);
            }          
        }
    }
}
