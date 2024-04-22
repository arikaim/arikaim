<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Drivers;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Console\ConsoleHelper;

/**
 * Drivers list command
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
        $this->setName('drivers:list')->setDescription('Drivers list');
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
      
        $this->table()->setHeaders(['Status','Name','Display Name','Category','Version']);
        $this->table()->setStyle('compact');

        $items = $arikaim->get('driver')->getList();

        foreach ($items as $driver) {
            $label = ($driver['status'] == 1) ? ConsoleHelper::getLabelText('enabled','green') : ConsoleHelper::getLabelText('disabled','red');
            $row = [$label,$driver['name'],$driver['title'],$driver['category'],$driver['version']];
            $this->table()->addRow($row);
        }
     
        $this->table()->render();
        $this->newLine();

        $this->showCompleted();
    }
}
