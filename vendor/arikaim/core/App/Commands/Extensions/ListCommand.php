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

use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Arikaim;

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
    protected function configure()
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
        $table = new Table($output);
        $table->setHeaders(['Name', 'Version', 'Status','Installed']);
        $table->setStyle('compact');

        $this->showTitle('Extensions');
     
        $manager = Arikaim::packages()->create('extension');
        $items = $manager->getPackages();
        
        $rows = [];
        foreach ($items as $name) {
            $package = $manager->createPackage($name);
            $extension = $package->getProperties();

            $status = ConsoleHelper::getStatusText($extension->status);
            $installed = ConsoleHelper::getYesNoText($extension->installed);

            $row = [$extension->name,$extension->version,$status,$installed];
            array_push($rows,$row);
        }
        
        $table->setRows($rows);
        $table->render();
        $this->style->newLine();
    }
}
