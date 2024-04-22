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

use Arikaim\Core\Console\ConfigProperties;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\Interfaces\ConfigPropertiesInterface;

/**
 * job details command
 */
class JobDetailsCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('job:details')->setDescription('Job details.');
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

        $job = $arikaim->get('queue')->create($name);

        if ($job == null) {
            $this->showError('Not valid job name!');
            return;
        } 

        $this->table()->setHeaders(['','']);
        $this->table()->setStyle('compact');   
              
        $jobDetails = $job->toArray();
        foreach($jobDetails as $key => $value) {
            $value = (\is_array($value) == true) ? Arrays::toString($value) : $value;
            if ($key == 'date_executed' || $key == 'schedule_time' || $key == 'next_run_date') {
                $value = DateTime::dateTimeFormat($value);
            }
            $row = [$key,$value];
            $this->table()->addRow($row);
        }
    
        $this->table()->render();
        $this->newLine();

        if ($job instanceof ConfigPropertiesInterface) {
            $this->newLine();
            $this->writeLn('Config properties',' ','cyan');
            $table = ConfigProperties::createPropertiesTable($job->getConfigProperties(),$output);
            $table->render();
        }

        $this->showCompleted();                
    }    
}
