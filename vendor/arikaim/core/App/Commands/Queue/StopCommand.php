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
use Arikaim\Core\System\Error\PhpError;
use Arikaim\Core\Arikaim;

/**
 * Queueu worker stop
 */
class StopCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('queue:stop');
        $this->setDescription('Queue worker stop');
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
        $this->showTitle('Stop queue worker');

        $worker = new QueueWorker(Arikaim::get('queue'),Arikaim::get('options'),Arikaim::get('logger'));
        $pid = $worker->getPid();
        
        $this->style->writeLn('Worker pid: ' . $pid);

        $result = ($pid != null) ? posix_kill($pid,15) : false;

        if ($result == true) {
            Arikaim::options()->set('queue.worker.pid',null);
            Arikaim::options()->set('queue.worker.command',null);  
            $this->style->writeLn('Done ' . $result);
        } else {
            $error = PhpError::getPosixError();
            $this->style->writeLn('Error: ' . $error);
        }
    }
}
