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
 * Extension info command
 */
class InfoCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('extensions:info')->setDescription('Extension Info');
        $this->addOptionalArgument('name','Extension Name');
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
     
        $this->table()->setHeaders(['', '']);
        $this->table()->setStyle('compact');

        $name = $input->getArgument('name');
        if (empty($name) == true) {
            $this->showError('Extension name required!');
            return;
        }
        
        $this->writeFieldLn('Name',$name);

        $manager = $arikaim->get('packages')->create('extension');
        $package = $manager->createPackage($name);
        if ($package == false) {
            $this->showError('Extension ' . $name . ' not exists!');
            return;
        }
        $extension = $package->getProperties();
        $this->newLine();
        $this->writeLn(ConsoleHelper::getDescriptionText($extension->description)); 
        $this->newLine();

        $rows = [
            ['Version',$extension->version],
            ['Class',$extension->class],
            ['Status',ConsoleHelper::getStatusText($extension->status)],
            ['Installed',ConsoleHelper::getYesNoText($extension->installed)]
        ];
            
        $this->table()->setRows($rows);
        $this->table()->render();
        $this->newLine();

        $this->showCompleted();
    }
}
