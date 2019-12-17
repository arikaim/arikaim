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
use Arikaim\Core\Arikaim;

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
    protected function configure()
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
        $this->showTitle('Enable cache.');
        
        Arikaim::config()->setBooleanValue('settings/cache',true);
        $result = Arikaim::config()->save();

        Arikaim::cache()->clear();
        
        if ($result == true) {
            $this->showCompleted();
        } else {
            $this->showError("Can't enable cache!");
        }
    }
}
