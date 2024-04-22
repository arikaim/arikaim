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

use Arikaim\Core\Console\ConsoleHelper;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Interfaces\Job\JobLogInterface;

/**
 * Run job command
 */
class RunJobCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('job:run')->setDescription('Run job from job registry.');
        $this->addOptionalArgument('name','Job Name');
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
        $name = $input->getArgument('name');
        if (empty($name) == true) {
            $this->showError('Job name required!');
            return;
        }
       
        $this->writeFieldLn('Name',$name);
   
        $job = $arikaim->get('queue')->run($name,null,null,
            function($mesasge) {
                $this->writeLn('  ' . ConsoleHelper::checkMark() . $mesasge);
            },function($error) {
                $this->writeLn('  ' . ConsoleHelper::errorMark() . ' Error ' . $error);
            }
        );
        
        if ($job->hasSuccess() == true) {                                         
            $this->showCompleted();    
        } else {
            // error
            $this->showError('Error');
            $this->showErrorDetails($job->getErrors());
            if ($job instanceof JobLogInterface) {
                $arikaim->get('logger')->error('Failed to execute cron job,',['errors' => $job->getErrors()]);
            }
        }                 
    }    
}
