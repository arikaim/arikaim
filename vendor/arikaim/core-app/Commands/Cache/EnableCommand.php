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
 * Enable cache command
 * 
 */
class EnableCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name cache:clear 
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('cache:enable')->setDescription('Enable cache');
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
        
        $arikaim->get('config')->setBooleanValue('settings/cache',true);
        $result = $arikaim->get('config')->save();

        $arikaim->get('cache')->clear();
        
        if ($result !== true) {           
            $this->showError('CACHE_ENABLE_ERROR');
            return;
        } 
      
        $this->showCompleted();        
    }
}
