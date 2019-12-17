<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Update;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\System\Update;

/**
 * UI components update command class 
 */
class UiComponentsUpdateCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: update
     * @return void
     */
    protected function configure()
    {
        $this->setName('update:ui')->setDescription('Update UI components');
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
        $this->showTitle('Update UI components');
      
        $update = new Update('arikaim/ui-components');
        $update->update(false,true);
        $currentVersion = $update->getCurrentVersion();
       
        // updated
        $this->showCompleted('UI components updated successfully.');
        $this->style->writeLn('New version: ' . ConsoleHelper::getLabelText($currentVersion));
    }
}
