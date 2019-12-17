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

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Arikaim;

/**
 * Disable install page command class
 */
class DisableInstallCommand extends ConsoleCommand
{  
    /**
     * Command config
     * @return void
     */
    protected function configure()
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
        $this->showTitle('Disable install page');
      
        Arikaim::get('config')->setBooleanValue('settings/disableInstallPage',true);
        // save and reload config file
        Arikaim::get('config')->save();
        Arikaim::get('cache')->clear();
        
        $this->showCompleted();         
    }
}
