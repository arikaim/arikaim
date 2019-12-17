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

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Queue\QueueWorker;
use Arikaim\Core\System\System;
use Arikaim\Core\Arikaim;

/**
 * Queueu worker
 */
class WorkerCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('queue:worker');
        $this->setDescription('Queue worker');
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
        $worker = new QueueWorker(Arikaim::get('queue'),Arikaim::get('options'),Arikaim::get('logger'));

        // set unlimited execution tile
        System::setTimeLimit(0); 
        
        $this->showTitle('Queue worker');
        $this->style->writeLn('pid: ' . getmypid());

        $worker->setPid();
        $worker->saveCommand('php cli queue:worker');

        $worker->run();        
    }
}
