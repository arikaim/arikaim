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

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Queue\Cron;
use Arikaim\Core\Utils\Path;

use Arikaim\Core\Interfaces\Job\QueueStorageInterface;
use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Interfaces\Job\RecurringJobInterface;
use Arikaim\Core\Interfaces\Job\ScheduledJobInterface;
use Arikaim\Core\Interfaces\Job\JobProgressInterface;
use Arikaim\Core\Interfaces\Job\JobLogInterface;
use Arikaim\Core\Interfaces\QueueInterface;
use Arikaim\Core\Interfaces\LoggerInterface;
use Arikaim\Core\Interfaces\WorkerManagerInterface;

use Closure;
use Exception;

/**
 * Queue manager
 */
class QueueManager implements QueueInterface
{
    /**
     * Queue storage driver
     *
     * @var QueueStorageInterface|object
     */
    protected $driver;

    /**
     * Logger
     *
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Jobs registry
     *
     * @var object|null
     */
    protected $jobsRegistry;

    /**
     * Constructor
     *
     * @param QueueStorageInterface $driver
     */
    public function __construct(QueueStorageInterface $driver, ?LoggerInterface $logger = null)
    {       
        $this->setDriver($driver);       
        $this->logger = $logger; 
        $this->jobsRegistry = null;   
    }

    /**
     * Get jobs registry
     *
     * @return object
     */
    public function jobsRegistry(): object
    {
        if ($this->jobsRegistry != null) {
            $this->jobsRegistry;
        } 

        return $this->jobsRegistry = new \Arikaim\Core\Models\JobsRegistry();
    }

    /**
     * Create worker manager
     *
     * @param string|null $name
     * @param array|null $args
     * @return \Arikaim\Core\Interfaces\WorkerManagerInterface|null;
     */
    public function createWorkerManager(?string $name = null, ?array $args = null)
    {
        if (empty($name) == true || $name == 'cron') {
            return new Cron();
        }
        
        $manager = Factory::createInstance($name,$args);
        if ($manager === null) {
            throw new Exception('Not valid queue worker class',1);
            return null;
        }
        if (($manager instanceof WorkerManagerInterface) == false) {
            throw new Exception('Not valid queue worker class',1);
            return null;
        }

        return $manager;
    }

    /**
     * Set queue storage driver
     *
     * @param QueueStorageInterface $driver
     * @return void
     */
    public function setDriver(QueueStorageInterface $driver): void
    {
        $this->driver = $driver;
    }

    /**
     * Get queue storage driver
     *
     * @return QueueStorageInterface
     */
    public function getStorageDriver(): QueueStorageInterface
    {
        return $this->driver;
    }

    /**
     * Return tru if job exist
     *
     * @param mixed $id
     * @return boolean
     */
    public function has($id): bool
    {
        return $this->driver->hasJob($id);
    }

    /**
     * Delete jobs
     *
     * @param array $filter
     * @return boolean
     */
    public function deleteJobs(array $filter = []): bool
    {
        return $this->driver->deleteJobs($filter);
    }

    /**
     * Create job instance from file in storage path
     *
     * @param string      $storagePath
     * @param string|null $className
     * @param array       $params
     * @return JobInterface|null
     */
    public function createFromStorage(string $storagePath, ?string $className = null, array $params = []): ?JobInterface
    {
        $fileName = (empty($className) == false) ? $className . '.php' : '';
        $path = Path::STORAGE_PATH . $storagePath . $fileName;

        $job = (\file_exists($path) == true) ? require($path) : null;
        if ($job instanceof JobInterface) {
            return $job;
        }
        if (\class_exists($className ?? '') == false) {
            return null;
        }

        $job = new $className(null,null,$params);

        return ($job instanceof JobInterface) ? $job : null;
    }

    /**
     * Create job obj
     *
     * @param string $class  job class or name
     * @param string|null $extension
     * @param array|null $params
     * @return JobInterface|null
     */
    public function create(string $class, ?array $params = null, ?string $extension = null): ?JobInterface
    {       
        $job = Factory::createJob($class,$extension,$params ?? []);
        if ($job instanceof JobInterface) {
            return $job;
        }

        // create from registry
        $model = $this->jobsRegistry()->findJob($class);
        if ($model != null) {
            $params = ($params == null) ? $model->options : $params;
            $job = Factory::createJob($model->handler_class ?? '',$model->package_name,$params);
        }            
      
        return $job;
    }

    /**
     * Create job intence from array 
     *
     * @param array $data  
     * @return JobInterface|null
     */
    public function createJobFromArray(array $data): ?JobInterface
    {      
        $class = $data['handler_class'] ?? null;
        $extension = $data['extension_name'] ?? null;
        $scheduleTime = $data['schedule_time'] ?? 0;
        $recuringInterval = $data['recuring_interval'] ?? '';
        $config = $data['options'] ?? null;

        $job = Factory::createJob($class,$extension,$config ?? []);       
        if ($job == null) {
            return null;
        }
       
        $job->setId($data['uuid'] ?? null);
        $job->setName($data['name'] ?? null);
        $job->setStatus($data['status'] ?? JobInterface::STATUS_CREATED);
        $job->setPriority($data['priority'] ?? 0);
        $job->setExtensionName($data['extension_name'] ?? null);
        $job->setDateExecuted($data['date_executed'] ?? null);
        $job->setDateCreated($data['date_created'] ?? null);
        $job->setQueue($data['queue'] ?? null);

        if ($job instanceof ScheduledJobInterface) {
            $job->setScheduleTime($scheduleTime);
        }
        if ($job instanceof RecurringJobInterface) {
            $job->setRecurringInterval($recuringInterval);
        }
       
        return $job;
    }

    /**
     * Update job config param
     *
     * @param string $id Job id
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function updateJobParam($id, string $key, $value): bool
    {
        return $this->driver->saveJobConfigParam($id, $key, $value);
    }

    /**
     * Find job by name, id or uuid
     *
     * @param string|integer $id Job id, uiid or name
     * @return array|null
     */
    public function getJob($id): ?array
    {
        return $this->driver->getJob($id);         
    }

    /**
     * Get recurring jobs
     *
     * @param string|null $extension
     * @return array|null
     */
    public function getRecuringJobs(?string $extension = null): ?array
    {   
        $filter = [
            'recuring_interval' => '*',
            'extension_name'    => (empty($extension) == true) ? '*' : $extension    
        ];       

        return $this->driver->getJobs($filter);        
    }

    /**
     * Get jobs
     *
     * @param array $filter
     * @return array|null
     */
    public function getJobs(array $filter = []): ?array
    {  
        return $this->driver->getJobs($filter);   
    }

    /**
     * Get all jobs due
     * 
     * @param string|null $jobName
     * @return array|null
     */
    public function getJobsDue(?string $jobName = null): ?array
    {
        return $this->driver->getJobsDue($jobName);
    }

    /**
     * Push job to queue
     *
     * @param string $name  job name or class
     * @param array|null $params
     * @param string|null $extension Extension package name
     * @param string|null $recuringInterval
     * @param string|null $scheduleTime
     * @return bool
     */
    public function push(
        string $name, 
        ?array $params = null,
        ?string $extension = null,
        ?string $recuringInterval = null,
        ?int $scheduleTime = null       
    ): bool
    {
        $job = $this->create($name,$params,$extension);
        if ($job == null) {
            return false;
        }
    
        return $this->addJob($job,$extension,false,$recuringInterval,$scheduleTime,$params);       
    }

    /**
     * Add job to queue
     *
     * @param JobInterface $job
     * @param string|null $extension
     * @param bool $disabled
     * @param string|null $recuringInterval
     * @param int|null $scheduleTime
     * @param array|null $config
     * @return bool
     */
    public function addJob(
        JobInterface $job, 
        ?string $extension = null, 
        bool $disabled = false,
        ?string $recuringInterval = null,
        ?int $scheduleTime = null,
        ?array $config = null
    ): bool
    {             
        if (empty($scheduleTime) == true) {
            $scheduleTime = ($job instanceof ScheduledJobInterface) ? $job->getScheduleTime() : $scheduleTime;
        }

        if (empty($recuringInterval) == true) {
            $recuringInterval = ($job instanceof RecurringJobInterface) ? $job->getRecurringInterval() : $recuringInterval;
        }

        return $this->driver->addJob([
            'priority'          => $job->getPriority(),
            'name'              => $job->getName(),
            'handler_class'     => \get_class($job),         
            'extension_name'    => $extension ?? $job->getExtensionName(),
            'status'            => ($disabled == false) ? JobInterface::STATUS_PENDING : JobInterface::STATUS_SUSPENDED,
            'recuring_interval' => $recuringInterval,
            'schedule_time'     => $scheduleTime,
            'config'            => ($config != null) ? \json_encode($config) : null,
            'uuid'              => $job->getId()
        ]);      
    }

    /**
     * Delete job
     *
     * @param string|integer $id Job id, uiid
     * @return bool
     */
    public function deleteJob($id): bool
    {
        return $this->driver->deleteJob($id);
    }

    /**
     * Delete all jobs
     *    
     * @return boolean
     */
    public function clear(): bool
    {
        return $this->driver->deleteJobs();
    }

    /**
     * Get next job
     *
     * @return JobInterface|null
     */
    public function getNext(): ?JobInterface
    {
        $jobData = $this->driver->getNext();

        return (empty($jobData) == true) ? null : $this->createJobFromArray($jobData);                 
    }

    /**
     * Run job
     *
     * @param JobInterface|string $name Job class, name or job instance
     * @param array|null $params job params
     * @param string|null $extension  package extension name
     * @param Closure|null $onJobProgress
     * @param Closure|null $onJobProgressError
     * @return JobInterface|null
     */
    public function run(
        $name, 
        ?array $params = null,
        ?string $extension = null, 
        ?Closure $onJobProgress = null, 
        ?Closure $onJobProgressError = null
    ): ?JobInterface
    {
        $job = ($name instanceof JobInterface) ? $name : $this->create($name,$params,$extension);
           
        return ($job != null) ? $this->executeJob($job,$onJobProgress,$onJobProgressError) : null;
    }

    /**
     * Execute job
     *
     * @param JobInterface $job
     * @param Closure|null $onJobProgress
     * @param Closure|null $onJobProgressError
     * @return JobInterface|null
    */
    public function executeJob(JobInterface $job, ?Closure $onJobProgress = null, ?Closure $onJobProgressError = null): ?JobInterface
    {
        if ($job instanceof JobProgressInterface) {
            $job->onJobProgress($onJobProgress);
            $job->onJobProgressError($onJobProgressError);
        }
        if ($job->getStatus() == JobInterface::STATUS_SUSPENDED) {
            $job->addError('Job is suspended.');
            return $job;
        }

        try {
    
            $job->execute();    
            if ($job->hasSuccess() == true) {
                // set status
                $this->driver->updateExecutionStatus($job);
            } else {
                // set error status
                $this->driver->setJobStatus($job->getId(),JobInterface::STATUS_ERROR);
            }
            
            if (($job instanceof JobLogInterface) && (empty($this->logger) == false)) {
                $this->logger->info($job->getLogMessage(),['job-name' => $job->getName() ]);
            }
        } catch (\Exception $e) {
            $job->addError($e->getMessage());     
            if (($job instanceof JobLogInterface) && (empty($this->logger) == false)) {              
                $this->logger->error($e->getMessage(),$job->toArray());                
            } 
        }
      
        return $job;
    }   
}
