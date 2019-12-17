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

use Symfony\Component\Console\Helper\Table;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Arikaim;

/**
 * Module info command
 */
class InfoCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('modules:info')->setDescription('Show module details');
        $this->addOptionalArgument('name','Module Name');
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
        $name = $input->getArgument('name');
        $this->style->text('Module ' . $name);
        $this->style->newLine();

        $table = new Table($output);
        $table->setHeaders(['','']);
        $table->setStyle('compact');

        $manager = Arikaim::packages()->create('module');
        $package = $manager->createPackage($name);
        if ($package == false) {
            $this->showError("Module $name not exists!");
            return;
        }
        $module = $package->getProperties(true);

        $bootable = ($module['bootable'] == 1) ? "yes" : "no";
        $installed = ($module['installed'] == true) ? "yes" : "no";
        $status = ($module['status'] == 1) ? "enabled" : "disabled";
        $rows = [
            ['Title',$module['title']],
            ['Description',$module['description']],
            ['Version',$module['version']],
            ['Service Name',$module['service_name']],
            ['Class',$module['class']],
            ['Bootable',$bootable],
            ['Type',$module['type']],
            ['Status',$status],
            ['Installed',$installed]
        ];

        $table->setRows($rows);
        $table->render();
        $this->style->newLine();
    }    
}
