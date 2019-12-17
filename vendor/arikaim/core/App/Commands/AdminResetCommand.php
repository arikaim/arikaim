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

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Db\Model;

/**
 * Reset control panel user command
 */
class AdminResetCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: install
     * @return void
     */
    protected function configure()
    {
        $this->setName('admin:reset')->setDescription('Reset control panel user password');
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
        $this->showTitle('Reset control panel user password');
         
        $helper = $this->getHelper('question');
        $validator = function($value) {                
            if (empty(trim($value)) == true) {
                throw new \Exception('Cannot be empty');              
                return null;
            }
            return $value;
        };
        $question = new Question("Enter new password: ",null);    
        $question->setValidator($validator);      
        $newPassword = trim($helper->ask($input, $output, $question));
        
        $question = new Question("Repeat new passsord: ");
        $repeatPasword = trim($helper->ask($input, $output, $question));

        if ($newPassword != $repeatPasword) {
            $this->showError("New password and repeat password mot mach!");
            return;
        }
        $user = Model::create('Users')->getControlPanelUser();
        if (is_object($user) == false) {
            $this->showError("Missing control panel user!");
            return;
        }
        
        $result = $user->changePassword($user->id,$newPassword);
        if ($result == true) {
            $this->showCompleted();            
        } else {
            $this->showError("Can't change control panel user password!");
        }

        return;
    }
}
