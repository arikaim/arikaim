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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\App\Install;
use Arikaim\Core\Arikaim;

/**
 * Install command class
 */
class InstallCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: install
     * @return void
     */
    protected function configure()
    {
        $this->setName('install')->setDescription('Arikaim Install');
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
        $this->showTitle('Arikaim CMS installation');
      
        if (Install::isInstalled() == true) {           
            $this->style->newLine();
        }
    
        //Requirements
        $this->style->text('Requirements');
        $this->style->newLine();
        $requirements = Install::checkSystemRequirements();

        // status - 0 red , 1 - ok,  2 - oarange
        foreach ($requirements['items'] as $item) {
            if ($item['status'] == 1) {
                $label = "\t" . ConsoleHelper::checkMark() . " " . ConsoleHelper::getLabelText($item['message'],'green');               
            } else {
                $label = "\t" . " " . ConsoleHelper::getLabelText($item['message'],'red');    
            }
            $this->style->writeLn($label);
        }

        if (count($requirements['errors']) > 0) {
            $this->style->newLine();
            $this->style->writeLn(ConsoleHelper::getDescriptionText('Errors'));
            foreach ($requirements['errors'] as $error) {
                $label = ConsoleHelper::getLabelText($error,'red');
                $this->style->writeLn($label);
            }
        }
        $this->style->newLine();
        $this->style->text(ConsoleHelper::getDescriptionText('Database'));
         
        $helper = $this->getHelper('question');
        $validator = function($value) {                
            if (empty(trim($value)) == true) {
                throw new \Exception('Cannot be empty');              
                return null;
            }
            return $value;
        };
        $question = new Question("\t Enter database Name: ",null);    
        $question->setValidator($validator);      
        $databaseName = $helper->ask($input, $output, $question);
        
        $question = new Question("\t Enter database Username: ");
        $question->setValidator($validator);      
        $databaseUserName = $helper->ask($input, $output, $question);

        $question = new Question("\t Enter database Password: ");
        $question->setValidator($validator);      
        $databasePassword = $helper->ask($input, $output, $question);

        $this->style->newLine();
        $question = new ConfirmationQuestion ("\t Start installation [yes]: ",true);
        $start = $helper->ask($input, $output, $question);     
        $this->style->newLine();

        if ($start == 1) {           
            // save config file               
            Arikaim::get('config')->setValue('db/username',$databaseUserName);
            Arikaim::get('config')->setValue('db/password',$databasePassword);
            Arikaim::get('config')->setValue('db/database',$databaseName);         
            Arikaim::get('config')->save();
              
            $result = Arikaim::get('db')->testConnection(Arikaim::get('config')->get('db'));
            if ($result == false) {
                $this->showError("Can't connect to db!");
                return;
            }
            $install = new Install();
            $result = $install->install();   
            if ($result == true) {
                $this->showCompleted();  
                return;          
            } 
            $this->showError("Error");
        }
        return;
    }
}
