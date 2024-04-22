<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Cache;

use Arikaim\Core\Console\ConsoleCommand;

/**
 * Disable cache command
 * 
 */
class DisableCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name cache:clear 
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('cache:disable')->setDescription('Disable cache');
    }

    /**
     * Command code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function executeCommand($input, $output)
    {
        global $arikaim;

        $this->showTitle();
        $arikaim->get('cache')->clear();
        
        $arikaim->get('config')->setBooleanValue('settings/cache',false);
        $result = $arikaim->get('config')->save();

        if ($result !== true) {           
            $this->showError('CACHE_DISABLE_ERROR');
            return;
        } 

        $this->showCompleted();
    }
}
