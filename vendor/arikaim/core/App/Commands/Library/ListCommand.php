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

use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Arikaim;

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
    protected function configure()
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
        $this->showTitle('UI library');
      
        $table = new Table($output);
        $table->setHeaders(['Name', 'Version', 'Type']);
        $table->setStyle('compact');

        $manager = Arikaim::packages()->create('library');
        $items = $manager->getPackages();

        $rows = [];
        foreach ($items as $name) {
            $library_package = $manager->createPackage($name);

            $library = $library_package->getProperties();
            $label = ($library->framework == true) ?  ConsoleHelper::getLabelText('framework','cyan') : '';
            $row = [$library->name,$library->version,$label];
            array_push($rows,$row);
        }

        $table->setRows($rows);
        $table->render();
        $this->style->newLine();
    }
}
