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
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\Interfaces\WorkerManagerInterface;

/**
 * Cron jobs 
 */
class Cron implements WorkerManagerInterface
{
    /**
     * Cron command 
     *
     * @var string
     */
    private static $command = 'cli scheduler >> /dev/null 2>&1';

    /**
     * Crontab interval
     *
     * @var string
     */
    private $interval = '5';

    /** 
     * Current user
     * 
     * @var string
    */
    private $currentUser;

    /**
     * Constructor
     *
     * @param string $interval
     */
    public function __construct(string $interval = '5')
    {
        $this->interval = $interval;
        $this->currentUser = Process::getCurrentUser()['name'];
    }

    /**
     * Get host
     *
     * @return string
    */
    public function getPort(): string
    {
        return '';
    }

    /**
     * Get port
     *
     * @return string
    */
    public function getHost(): string
    {
        return '';
    }

    /**
     * Get title
     *    
     * @return string
     */
    public function getTitle(): string
    {
        return 'Cron Scheduler';
    }

    /**
     * Get description
     *    
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return 'Crontab queue worker';
    }

    /**
     * Get cron command
     * 
     * @param string $minutes
     * @return string
     */
    public static function getCronCommand(string $minutes = '5'): string
    {
        $php = (Process::findPhp() === false) ? 'php' : Process::findPhp();
        
        return "*/$minutes * * * * " . $php . ' ' . ROOT_PATH . BASE_PATH . '/'. Self::$command;      
    }
    
    /**
     * Run cron command
     *
     * @return mixed|false
     */
    public static function runCronCommand()
    {
        $php = (Process::findPhp() === false) ? 'php' : Process::findPhp();
        $command = $php . ' ' . ROOT_PATH . BASE_PATH . '/cli scheduler';  

        $result = Process::runShellCommand($command);

        return $result;
    }

    /**
     * Retrun true if command is crontab command
     *
     * @param string $command
     * @return boolean
     */
    public static function isCronCommand(string $command): bool
    {
        $len = \strlen(Self::$command);

        return (substr($command,-$len) == Self::$command);
    }

    /**
     * Reinstall cron entry for scheduler
     *
     * @return mixed
     */
    public function reInstall()
    {    
        $this->stop();

        return $this->run();
    }

    /**
     * Add cron entry for scheduler
     * 
     * @return bool
     */
    public function run(): bool
    {          
        $this->addJob(Self::getCronCommand($this->interval));

        return $this->hasJob(Self::getCronCommand($this->interval));
    }

    /**
     * Remove cron entry for scheduler
     *
     * @return mixed
     */
    public function stop(): bool
    {
        $jobs = $this->getJobs();
        foreach ($jobs as $command) {
            if (Self::isCronCommand($command) == true) {
                $this->removeJob($command);  
            }
        }

        return !$this->isRunning();
    }

    /**
     * Return true if crontab entry is exists
     *
     * @return boolean
     */
    public function isRunning(): bool
    {
        $jobs = $this->getJobs();
     
        foreach ($jobs as $command) {
            if (Self::isCronCommand($command) == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if crontab have jobs
     *
     * @param array $jobs
     * @return boolean
     */
    public function hasJobs($jobs): bool
    {
        $msg = 'no crontab for';
        
        return (empty($jobs) == true || \preg_match("/{$msg}/i", $jobs[0]) == true) ? false : true;
    }
    
    /**
     * Get crontab jobs
     *
     * @return array
     */
    public function getJobs(): array 
    {
        $output = Process::runShellCommand('crontab -l -u ' . $this->currentUser);
      
        $output = (empty($output) == true) ? [] : $output;
        $lines = (\is_array($output) == false) ? \explode("\n",$output) : $output;
        $jobs = [];

        foreach ($lines as $line) {
            $line = \trim($line);
            if (empty($line) == false) {
                $jobs[] = $line;
            }          
        }

        return ($this->hasJobs($jobs) == true) ? $jobs : [];
    }

    /**
     * Return true if crontab have job
     *
     * @param string $command
     * @return boolean
     */
    public function hasJob(string $command): bool
    {
        $commands = $this->getJobs();  

        return \in_array($command,$commands); 
    }   

    /**
     * Add cron tab job
     *
     * @param string $command
     * @return mixed|false
     */
    public function addJob(string $command)
    {
        if ($this->hasJob($command) == true) {
            return true;
        }
    
        $jobs = $this->getJobs();
        $jobs[] = $command;

        return $this->addJobs($jobs);
    }

    /**
     * Add cron tab jobs
     *
     * @param array $commands
     * @return mixed|false
     */
    public function addJobs(array $commands) 
    {
        foreach ($commands as $key => $command) {   
            if (empty($command) == true) {
                unset($commands[$key]);
            }
        }
       
        $text = \trim(Arrays::toString($commands));
        if (empty($text) == false) {
            $cmd = 'echo "'. $text .'" | crontab -u ' . $this->currentUser . ' - ';
            $result = Process::runShellCommand($cmd);

            return $result;
        } 

        return $this->removeAll();       
    }

    /**
     * Remove all job from crontab
     *
     * @return mixed
     */
    public function removeAll()
    {
        return Process::runShellCommand('crontab -r -u ' . $this->currentUser);
    }

    /**
     * Delete crontab job
     *
     * @param string $command
     * @return bool
     */
    public function removeJob($command): bool 
    {
        if ($this->hasJob($command) == true) {
            $jobs = $this->getJobs();
            unset($jobs[\array_search($command,$jobs)]);

            return ($this->addJobs($jobs) !== false);
        }
        
        return true;
    }

    /**
     * get cron details.
     *  
     * @return array
     */
    public function getDetails(): array
    {
        return [
            'command'  => $this->getCronCommand($this->interval),  
            'jobs'     => $this->getJobs(),  
            'interval' => $this->interval,     
            'user'     => $this->currentUser
        ];
    }
}
