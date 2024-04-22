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
use Arikaim\Core\App\Install;
use Arikaim\Core\App\PostInstallActions;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\App\SystemUpdate;


/**
 * Repair (reinstall) command class
 */
class RepairInstallCommand extends ConsoleCommand
{  
    /**
     * Command config
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('install:repair')->setDescription('Arikaim CMS Repair Installation');
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
        $doneMsg = '  ' . ConsoleHelper::checkMark() . ' ';

        // update 
        SystemUpdate::run(function($message) use($doneMsg) {                  
            $this->style->writeLn($doneMsg . $message);
        });

        $install = new Install();
        $this->style->text(ConsoleHelper::getDescriptionText('Core'));
        $this->style->newLine();
        $result = $install->install(
            function($message) use($doneMsg) {                  
                $this->style->writeLn($doneMsg . $message);
            },function($error) {                  
                $this->style->writeLn("\t " . ConsoleHelper::getLabelText($error,'red'));  
            }
        );   

        if ($result == false) {
            $this->showError('Error');
            return;
        }

        // set row format to dynamic 
        $install->systemTablesRowFormat();
      
        // run post install actions
        $this->style->newLine();
        $this->style->text(ConsoleHelper::getDescriptionText('Post install actions'));
        PostInstallActions::run(
            function($package) use($doneMsg) {   
                $this->style->writeLn($doneMsg . $package . ' action executed.');
            },function($package) { 
                $error = 'Error in package ' . $package;  
                $this->style->writeLn("\t " . ConsoleHelper::getLabelText($error,'red'));
            }
        );

        if ($result == true) {
            $this->showCompleted();  
        } else {
            $this->showError('Error');
        }
    }
}
