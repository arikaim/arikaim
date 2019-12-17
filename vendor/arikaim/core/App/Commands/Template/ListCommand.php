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

use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Arikaim;

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
    protected function configure()
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
        $this->showTitle('Themes');
      
        $table = new Table($output);
        $table->setHeaders(['Name','Version','Status']);
        $table->setStyle('compact');
        
        $current = Arikaim::options()->get('current.template',null);
    
        $manager = Arikaim::packages()->create('template');
        $items = $manager->getPackages();

        $rows = [];
        foreach ($items as $name) {
            $package = $manager->createPackage($name);
            $template = $package->getProperties();
            $label = ($template['name'] == $current) ? ConsoleHelper::getLabelText('current','cyan') : '';
            $row = [$template['name'],$template['version'],$label];

            array_push($rows,$row);
        }

        $table->setRows($rows);
        $table->render();
        $this->style->newLine();
    }
}
