<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Queue;

use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Utils\DateTime;

/**
 * Queue jobs list command
 */
class JobsCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('queue:jobs')->setDescription('Jobs list.');
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
        $this->showTitle('Recurring Jobs');
        $this->table()->setHeaders(['','Next Run Time','Handler']);
        $this->table()->setStyle('compact');

        // Recurring jobs
        $items = $arikaim->get('queue')->getRecuringJobs();
        foreach ($items as $item) {                  
            $row = ['',DateTime::dateTimeFormat($item['due_date']),$item['handler_class']];          
            $this->table()->addRow($row);
        }       
        $this->table()->render();

        // Scheduled jobs
        $this->showTitle('Scheduled Jobs');
        $this->table()->setHeaders(['','Scheduled Time','Handler']);

        $items = $arikaim->get('queue')->getJobs(['schedule_time' => '*']);
        $rows = [];
        foreach ($items as $item) {                  
            $row = ['',DateTime::dateTimeFormat($item['schedule_time']),$item['handler_class']];
            $rows[] = $row;
        }
        if (\count($rows) == 0) {
            $rows[] = ['..',''];
        }
        $this->table()->setRows($rows);
        $this->table()->render();

        $this->showCompleted(); 
    }    
}
