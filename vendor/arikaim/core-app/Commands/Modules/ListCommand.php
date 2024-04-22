<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Modules;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Console\ConsoleCommand;

/**
 * Modules list command
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
        $this->setName('modules:list')->setDescription('Modules list.');
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
        $this->table()->setHeaders(['Name','Version','Type','Status']);
        $this->table()->setStyle('compact');

        $manager = $arikaim->get('packages')->create('module');
        $items = $manager->getPackages();

        foreach ($items as $name) {
            $package = $manager->createPackage($name);
            $module = $package->getProperties(true);

            $installedLabel = ($module->installed == true) ?  ConsoleHelper::getLabelText('installed','cyan') : '';
            $statusLabel = ($module->status == 1) ?  ConsoleHelper::getLabelText('enabled','green') : '';
            $label = $installedLabel . ' ' . $statusLabel;
            $row = [$module->name,$module->version,$module->type,$label];
            $this->table()->addRow($row);
        }
        $this->table()->render();
        
        $this->showCompleted(); 
    }    
}
