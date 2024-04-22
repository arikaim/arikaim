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

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\App\Install;

/**
 * Prepare install command class
 */
class PrepareCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: install
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('install:prepare')->setDescription('Check install requirements.');
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
             
        $install = new Install();

        //Requirements             
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

        $this->style->newLine();

        $result = $install->prepare(
            function($messge) {
                $msg =  ConsoleHelper::checkMark() . $messge;
                $this->style->writeLn($msg);
            },
            function($error) {
                $this->style->writeLn(ConsoleHelper::errorMark() . ConsoleHelper::getLabelText($error,'red'));
            },$requirements
        );

        if ($result == true) {
            $this->showCompleted();  
        } else {
            $this->showError('Error');  
        }
    }
}
