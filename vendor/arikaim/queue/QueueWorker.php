<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Queue;

use Arikaim\Core\System\Process;
use Arikaim\Core\System\System;
use Arikaim\Core\Interfaces\QueueInterface;
use Arikaim\Core\Interfaces\OptionsInterface;

/**
 * Queue worker
 */
class QueueWorker
{
    const STOP  = 1;
    const PAUSE = 2;
    const RUN   = 3;

    /**
     * Queue manager
     *
     * @var object
     */
    private $manager;

    /**
     * Worker process 
     *
     * @var object
     */
    private $process;

    /**
     * Worker status
     *
     * @var boolean
     */
    private $status;

    /**
     * Options
     *
     * @var OptionsInterface
     */
    private $options;

    /**
     * Constructor
     * 
     * @param QueueInterface|null $driver
     */
    public function __construct(QueueInterface $manager, OptionsInterface $options, $logger)
    {
        $this->manager = $manager;
        $this->process = null;
        $this->status = Self::RUN;
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * Run queue worker daemon
     *
     * @return void
     */
    public function run()
    {
        $this->status = Self::RUN;

        // set signal handlers       
        pcntl_signal(15,[$this,"handleSignals"]);
        pcntl_signal(SIGINT,[$this,"handleSignals"]);

        while(true) {        
            pcntl_signal_dispatch();
            $this->checkStatus();

            $job = $this->manager->getNext();
            if (is_object($job) == true) {
                try {
                    $result = $this->manager->executeJob($job);
                } catch (\Exception $e) {
                    $this->logger->error('Job execution error! Job Id: ' . $job->getId());
                }
            }
        }
    }

    /**
     * Signal handler
     *
     * @param ineteger $signo
     * @return void
     */
    protected function handleSignals($signo)
    {
        switch ($signo) {
            case SIGINT: {
                $this->status = Self::STOP;
                break;
            }
            case 15: { // SIGTERM
                $this->status = Self::STOP;
                break;
            }
            case 19: { // SIGSTOP 
                $this->status = Self::STOP;
                break;
            }
        } 
    }

    /**
     * Check worker status
     *
     * @return void
     */
    protected function checkStatus()
    {
        switch($this->status) {
            case Self::STOP: {
                System::writeLine("Stop");   
                if (empty($this->process) == false) {
                    $this->process->stop(1);    
                }         
                exit(0);
            }
        }
    }

    /**
     * Return true if worker process is running
     *
     * @return boolean
     */
    public function isRunning()
    {
        $pid = $this->options->get('queue.worker.pid',null);

        return (empty($pid) == false) ? Process::isRunning($pid) : false;          
    }

    /**
     * Get worker process pid
     *
     * @return integer
     */
    public function getPid()
    {
        $pid = $this->options->get('queue.worker.pid',null);
        
        return $pid;    
    }

    /**
     * Stop worker daemon process
     *
     * @return void
     */
    public function stopDaemon()
    {
        $pid = $this->getPid();
        if ($pid != null) {
            posix_kill(intval($pid),15);
            sleep(2);
            return posix_get_last_error();
        }
        
        return false;
    }

    /**
     * Save worker pid in options
     *
     * @param integer|null $pid
     * @return void
     */
    public function setPid($pid = null)
    {
        $pid = (empty($pid) == true) ? getmypid() : $pid;
        $this->options->set('queue.worker.pid',$pid,false);
    }

    /**
     * Save worker command
     *
     * @param string $command
     * @return void
     */
    public function saveCommand($command)
    {
       $this->options->set('queue.worker.command',$command);
    }

    /**
     * Run daemon worker process
     *
     * @return bool
     */
    public function runDaemon()
    {
        $this->process = Process::create('php cli queue:worker',null,null,null);
        $this->process->start();
        sleep(1);

        if ($this->process->isRunning() == true) {
            $pid = $this->process->getPid();
            $this->setPid($pid);
            $this->saveCommand($this->process->getCommandLine());
            return !empty($pid);
        }

        return false;
    }

    /**
     * Get worker process
     *
     * @return object
     */
    public function getProcess()
    {
        return $this->process;
    }
}
