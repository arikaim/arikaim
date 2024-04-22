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

/**
 * Disable install page command class
 */
class DisableInstallCommand extends ConsoleCommand
{  
    /**
     * Command config
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('install:disable')->setDescription('Disable install page');
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
      
        $arikaim->get('config')->setBooleanValue('settings/disableInstallPage',true);
        // save and reload config file
        $arikaim->get('config')->save();
        $arikaim->get('cache')->clear();
        
        $this->showCompleted();         
    }
}
