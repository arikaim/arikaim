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

use Arikaim\Installer\ComposerEvents;

use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Console\ConsoleCommand;
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Arikaim\Core\Packages\Composer;

/**
 * Composer command class
 */
class ComposerCommand extends ConsoleCommand
{  
    /**
     * Command config  
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('composer')->setDescription('Composer');
        $this->addOptionalArgument('composer-command','Composer command');
        $this->addOptionalArgument('package','Composer package name');
        $this->addOptionalOption('option','Composer option');
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
        $this->showTitle('Composer');
        
        $command = $input->getArgument('composer-command');
        $packageName = $input->getArgument('package');
        $option = $input->getOption('option');

        if (empty($option) == false) {
           $command .= ' -' . $option;
        }
    
        ComposerEvents::onPreUpdate(function($event) {
            $this->showTitle('Updating packages ...');    
        });

        ComposerEvents::onPackageUpdate(function($package) {
            $this->writeLn(ConsoleHelper::checkMark() . $package->getName());   
        });

        ComposerEvents::onPackageInstall(function($package) {
            $this->writeLn(ConsoleHelper::checkMark() . $package->getName());   
        });

        Composer::run($command,$packageName);  

        $this->showCompleted();       
    }
}
