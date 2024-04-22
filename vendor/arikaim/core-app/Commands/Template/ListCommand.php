<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Template;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;

/**
 * Templates list command
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
        $this->setName('theme:list')->setDescription('Themes list');
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
        $this->table()->setHeaders(['Name','Version','Status']);
        $this->table()->setStyle('compact');
        
        $current = $arikaim->get('options')->get('current.template',null);
    
        $manager = $arikaim->get('packages')->create('template');
        $items = $manager->getPackages();

        foreach ($items as $name) {
            $package = $manager->createPackage($name);
            $template = $package->getProperties();
            $label = ($template['name'] == $current) ? ConsoleHelper::getLabelText('current','cyan') : '';
            $row = [$template['name'],$template['version'],$label];
            $this->table()->addRow($row);          
        }
        $this->table()->render();
        
        $this->showCompleted();
    }
}
