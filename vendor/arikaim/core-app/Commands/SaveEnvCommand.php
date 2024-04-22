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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Save env vars command class
 */
class SaveEnvCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: env:save
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('env:save')->setDescription('Save environment vars.');
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
        $this->style->newLine();
     
        $helper = $this->getHelper('question');
     
        $question = new Question("\t Enter host: ");
        $question->setValidator(function($value) {                
            if (empty(trim($value)) == true) {
                throw new \Exception('Cannot be empty');              
                return null;
            }
            return $value;
        });      
        $host = $helper->ask($input, $output, $question);

        $question = new Question("\t Enter base path: ",null);        
        $basePath = $helper->ask($input, $output, $question);
        
        $arikaim->get('config')->setValue('environment/host',\trim($host));
        $arikaim->get('config')->setValue('environment/basePath',\trim($basePath));         
        $arikaim->get('config')->save();

        $this->style->newLine();
        $this->showCompleted();  
    }
}
