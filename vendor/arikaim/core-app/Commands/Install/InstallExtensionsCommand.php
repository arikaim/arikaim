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

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\App\Install;

/**
 * Install extensions command class
 */
class InstallExtensionsCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: install
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('install:extensions')->setDescription('Arikaim CMS Extensions Install');
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

        $helper = $this->getHelper('question');

        $this->style->newLine();
        $question = new ConfirmationQuestion ("\t Start installation [yes]: ",true);
        $start = $helper->ask($input, $output, $question);     
        $this->style->newLine();

        if ($start == 1) {           
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
            
            if ($result == true) {
                $this->showCompleted();  
            }           
        }
    }
}
