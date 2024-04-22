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
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\App\Install;
use Arikaim\Core\App\PostInstallActions;

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
    protected function configure(): void
    {
        $this->setName('install')->setDescription('Arikaim CMS Install');
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
        $install = new Install();

        // Requirements
        $this->style->text('Requirements');
        $this->newLine();
        $requirements = Install::checkSystemRequirements();
        // status - 0 red , 1 - ok,  2 - warning
        foreach ($requirements['items'] as $item) {            
            if ($item['status'] == 1) {
                $label = ConsoleHelper::checkMark();            
            } elseif ($item['status'] == 2) {
                $label = ConsoleHelper::warning();   
            }         
            $this->style->writeLn($label . $item['message']);
        }

        $install->prepare(
            function($messge) {
                $msg = ConsoleHelper::checkMark() . $messge;
                $this->style->writeLn($msg);
            },
            function($error) {
                $this->style->writeLn(ConsoleHelper::errorMark() . ConsoleHelper::getLabelText($error,'red'));
            },$requirements
        );
       
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
        $question = new ConfirmationQuestion("\t Start installation [yes]: ",true);
        $start = $helper->ask($input, $output, $question);     
        $this->style->newLine();

        if ($start == 1) {           
            // save config file               
            $arikaim->get('config')->setValue('db/username',$databaseUserName);
            $arikaim->get('config')->setValue('db/password',$databasePassword);
            $arikaim->get('config')->setValue('db/database',$databaseName);         
            $arikaim->get('config')->save();
              
            $result = $arikaim->get('db')->testConnection($arikaim->get('config')->get('db'));
            if ($result == false) {
                $this->showError("Can't connect to db!");
                return;
            }
                       
            $this->style->text(ConsoleHelper::getDescriptionText('Core'));
            $result = $install->install(
                function($message) {                                        
                    $this->style->writeLn(ConsoleHelper::checkMark() . $message);
                },function($error) {                  
                    $this->style->writeLn(ConsoleHelper::errorMark() . ConsoleHelper::getLabelText($error,'red'));  
                }
            );   
          
            if ($result == true) {
                // install modules
                $this->style->newLine();
                $this->style->text(ConsoleHelper::getDescriptionText('Modules'));
                $result = $install->installModules(
                    function($name) {                  
                        $this->style->writeLn(ConsoleHelper::checkMark() . $name);
                    },function($name) {                  
                        $this->style->writeLn(ConsoleHelper::errorMark() . ConsoleHelper::getLabelText('Error: ' . $name . ' module.','red'));  
                    }
                );                       
            } else {
                $this->showError('Error');
                return;
            } 

            if ($result == true) {
                // install extensions
                $this->style->newLine();
                $this->style->text(ConsoleHelper::getDescriptionText('Extensions'));
                $result = $install->installExtensions(
                    function($name) {                  
                        $this->style->writeLn(ConsoleHelper::checkMark() . $name);
                    },function($name) {                  
                        $this->style->writeLn(ConsoleHelper::errorMark() . ConsoleHelper::getLabelText('Error: ' . $name . ' extension.','red'));  
                    }
                );                
            } else {
                $this->showError('Error');
                return;
            } 

            if ($result == true) {
                // run post install actions
                $this->style->newLine();
                $this->style->text(ConsoleHelper::getDescriptionText('Post install actions'));
                PostInstallActions::run(
                    function($package) {   
                        $this->style->writeLn(ConsoleHelper::checkMark() . $package . ' action executed.');
                    },function($package) { 
                        $error = 'Error in package ' . $package;  
                        $this->style->writeLn(ConsoleHelper::errorMark() . ConsoleHelper::getLabelText($error,'red'));
                    }
                );
            } else {
                $this->showError('Error');
                return;
            } 

            $install->changeStorageFolderOwner();
            
            $this->showCompleted();  
        }
    }
}
