<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Extensions;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;

/**
 * Extensions list command
 */
class ListCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('extensions:list')->setDescription('Extensions list');
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {       
        global $arikaim;
        
        $this->showTitle();
        $this->table()->setHeaders(['Name','Version','Status','Installed']);
        $this->table()->setStyle('compact');

        $manager = $arikaim->get('packages')->create('extension');
        $items = $manager->getPackages();
    
        foreach ($items as $name) {
            $package = $manager->createPackage($name);
            $extension = $package->getProperties();

            $status = ConsoleHelper::getStatusText($extension->status ?? 0);
            $installed = ConsoleHelper::getYesNoText($extension->installed);
            $row = [$extension->name,$extension->version,$status,$installed]; 

            $this->table()->addRow($row);
        }
        
        $this->table()->render();
        $this->newLine();

        $this->showCompleted();
    }
}
