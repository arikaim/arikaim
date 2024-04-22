<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Library;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;

/**
 * Library list command
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
        $this->setName('library:list')->setDescription('UI library list');
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
        $this->table()->setHeaders(['Name','Version','Type']);
        $this->table()->setStyle('compact');

        $manager = $arikaim->get('packages')->create('library');
        $items = $manager->getPackages();

        foreach ($items as $name) {
            $package = $manager->createPackage($name);
            $library = $package->getProperties();
            $label = ($library->framework == true) ? ConsoleHelper::getLabelText('framework','cyan') : '';
            $row = [$library->name,$library->version,$label];
            $this->table()->addRow($row);
        }
        $this->table()->render();
        
        $this->showCompleted();    
    }
}
