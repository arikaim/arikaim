<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App\Commands\Job;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Arikaim;

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
    protected function configure()
    {
        $this->setName('job:run')->setDescription('Run job.');
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
        $this->showTitle('Run Job');
        $name = $input->getArgument('name');
        if (empty($name) == true) {
            $this->showError("Job name required!");
            return;
        }
        $this->style->writeLn('Name: ' . $name);
      
        if (Arikaim::queue()->has($name) == false) {
            $this->showError("Not valid job name!");
            return;
        } 
        $result = Arikaim::queue()->execute($name);
        
        if ($result === false) {
            $this->showError("Error execution job!");
        } else {
            $this->showCompleted();
        }      
    }    
}
