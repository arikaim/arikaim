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
use Arikaim\Core\Arikaim;
use Arikaim\Core\App\Install;

/**
 * Update command class 
 */
class UpdateCommand extends ConsoleCommand
{  
    /**
     * Command config
     * name: update
     * @return void
     */
    protected function configure()
    {
        $this->setName('update')->setDescription('Arikaim Update');
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
        $this->showTitle('Update Arikaim CMS');
      
        $packageName = Arikaim::getCorePackageName();

        $update = new Update($packageName);
        $update->update(false,true);
        $currentVersion = $update->getCurrentVersion();

        // updated
        $install = new Install();
        $result = $install->install();
        if ($result !== false) {
            $this->showCompleted('Arikaim CMS updated successfully.');
            $this->style->writeLn('New version: ' . ConsoleHelper::getLabelText($currentVersion));
        } else {
            $this->showError("Can't install Arikaim CMS core package!");
        }
    }
}
