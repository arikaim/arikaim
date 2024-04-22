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
use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\System\System;
use Exception;

/**
 * Process cron jobs
 */
class CronCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void 
    {
        $this->setName('scheduler');
        $this->setDescription('Cron scheduler');
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

        // unlimited execution time
        System::setTimeLimit(0); 
       
        $this->showTitle();

        $jobs = $arikaim->get('queue')->getJobsDue();
        $jobsDue = \count($jobs);

        $this->writeFieldLn('Jobs due ',$jobsDue); 
        $this->writeLn('...');
        $executed = 0;  

        if ($jobsDue > 0) {
            $executed = $this->runJobs($jobs);          
        }

        $this->writeFieldLn('Executed jobs ',$executed);
        $this->showCompleted(); 
    }

    /**
     * Run jobs due
     *
     * @param array $jobs
     * @return integer
     */
    protected function runJobs(array $jobs): int
    {
        global $arikaim;
        
        $executed = 0;  
        
        foreach ($jobs as $item) {
            $job = $arikaim->get('queue')->createJobFromArray($item);

            if ($job->isDue() == false) {    
                continue;
            }
          
            $name = (empty($job->getName()) == true) ? $job->getId() : $job->getName();
            
            try {
                $job = $arikaim->get('queue')->executeJob($job,
                    function($mesasge) {
                        $this->writeLn('  ' . ConsoleHelper::checkMark() . $mesasge);
                    },function($error) {
                        $this->writeLn('  ' . ConsoleHelper::errorMark() . ' Error ' . $error);
                    }
                );

                if ($job->hasSuccess() == true) {               
                    $this->writeLn(ConsoleHelper::checkMark() . $name);   
                    $executed++;                       
                } else {
                    $this->writeLn(ConsoleHelper::errorMark() . 'Job: ' . $name . ' Error: ' . $job->getErrors()[0]);
                    $arikaim->get('logger')->error('Failed to execute cron job,',['errors' => $job->getErrors()]);
                }
                
            } catch (Exception $e) {
                $arikaim->get('logger')->error('Failed to execute cron job,',['error' => $e->getMessage()]);
            }           
        }

        return $executed;
    }
}
