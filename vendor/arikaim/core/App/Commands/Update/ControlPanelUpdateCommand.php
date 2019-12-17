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
 * Control panel update command class 
 */
class ControlPanelUpdateCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: update
     * @return void
     */
    protected function configure()
    {
        $this->setName('update:admin')->setDescription('Update control panel');
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
        $this->showTitle('Update Control Panel');
      
        $update = new Update('arikaim/system-template');
        $update->update(false,true);
        $currentVersion = $update->getCurrentVersion();

        // updated
        $this->showCompleted('Control Panel updated successfully.');
        $this->style->writeLn('New version: ' . ConsoleHelper::getLabelText($currentVersion));
    }
}
