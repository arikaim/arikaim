<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Queue\Jobs;

use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Interfaces\Job\RecurringJobInterface;
use Arikaim\Core\Interfaces\Job\ScheduledJobInterface;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Queue\Jobs\JobPropertiesDescriptor;
use Arikaim\Core\Queue\Jobs\JobResult;
use Arikaim\Core\Utils\DateTime;

use Arikaim\Core\Collection\Traits\Descriptor;
use Arikaim\Core\Queue\Traits\Recurring;
use Arikaim\Core\Queue\Traits\Scheduled;

/**
 * Base class for all jobs
 */
abstract class Job implements JobInterface, RecurringJobInterface, ScheduledJobInterface 
{
    use 
        Descriptor,
        Scheduled,
        Recurring;

    /**
     * Unique job id 
     *
     * @var string|integer|null
     */
    protected $id = null;

    /**
     * Job name
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * Priority
     *
     * @var integer
     */
    protected $priority = 0;

    /**
     * Extension name
     *
     * @var string|null
     */
    protected $extension = null;
  
    /**
     * Job status
     *
     * @var int
     */
    protected $status = JobInterface::STATUS_CREATED;

    /**
     * Execution timestamp 
     *
     * @var int|null
     */
    protected $dateExecuted = null;

    /**
     * Date added to queue
     *
     * @var int|null
     */
    protected $dateCreated = null;

    /**
     * Queue name
     *
     * @var string|null
     */
    protected $queue = null;

    /**
     * Job params
     *
     * @var array
     */
    protected $params = [];

    /**
     * Job result
     *
     * @var object
     */
    protected $result;

    /**
     * Job code
     *
     * @return mixed
     */
    abstract public function execute();

    /**
     * Constructor
     *
     * @param string|null $extension
     * @param array $params
     */
    public function __construct(?string $extension = null, array $params = [])
    {
        $this->setExtensionName($extension);      
        $this->setPriority(0);
        $this->dateExecuted = null;      
        $this->status = JobInterface::STATUS_CREATED;       
        $this->params = $params;
        $this->id = null;
        $this->setDescriptorClass(JobPropertiesDescriptor::class);
        $this->result = new JobResult();
        $this->init();
    }

    /**
     * Return true if job is due
     *
     * @return boolean
    */
    public function isDue(): bool
    {
        // check for recurring
        if (empty($this->getRecurringInterval()) == false) {
            return ($this->getDueDate() <= DateTime::getCurrentTimestamp());
        }
       
        // check for scheduled
        if (empty($this->getScheduleTime()) == false) {
            return ($this->scheduleTime <= DateTime::getCurrentTimestamp());
        }

        return ($this->status == JobInterface::STATUS_PENDING || $this->status == JobInterface::STATUS_CREATED);
    } 

    /**
     * Get hjob params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
    
    /**
     * Set job params
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Set param
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParam(string $name, $value): void
    {
        $this->params[$name] = $value;
    } 

    /**
     * Get param value
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    } 

    /**
     * Init job
     *
     * @return void
     */
    public function init(): void
    {        
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'                => $this->getId(),
            'name'              => $this->getName(),
            'priority'          => $this->getPriority(),
            'status'            => $this->getStatus(),
            'date_executed'     => $this->getDateExecuted(),       
            'date_created'      => $this->getDateCreated(),          
            'extension_name'    => $this->getExtensionName(),
            'errors'            => $this->getErrors(),
            'handler_class'     => \get_class(),
            'queue'             => $this->getQueue(),
            'recuring_interval' => $this->interval ?? null,
            'next_run_date'     => $this->getDueDate(),
            'schedule_time'     => $this->scheduleTime ?? null
        ];
    }

    /**
     * Get job result object
     *
     * @return object
     */
    public function result(): object
    {
        return $this->result;
    }

    /**
     * Get execution errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return [$this->result->getError()];
    }

    /**
     * Add error
     *
     * @param string $errorMessage
     * @return void
     */
    public function addError(string $errorMessage): void
    {
        $this->result->error($errorMessage);
    }

    /**
     * Return true if job is executed successful
     *
     * @return boolean
     */
    public function hasSuccess(): bool
    {
        return ($this->result->hasError() == false);
    }

    /**
     * Get execution timestamp
     *   
     * @return int|null
    */
    public function getDateExecuted(): ?int
    {
        return $this->dateExecuted;
    }

    /**
     * Get date created
     *
     * @return integer|null
     */
    public function getDateCreated(): ?int
    {
        return $this->dateCreated;
    }

    /**
     * Set execution date
     *   
     * @param int|null $time  timestamp
     * @return void
    */
    public function setDateExecuted(?int $time): void
    {
        $this->dateExecuted = $time;
    }

    /**
     * Set date pused to queue
     *   
     * @param int|null $time  timestamp
     * @return void
    */
    public function setDateCreated(?int $time): void
    {
        $this->dateCreated = $time;
    }

    /**
     * Get job status
     *   
     * @return int
    */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set job status
     *
     * @param integer $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        if ($status == JobInterface::STATUS_CREATED) {
            $this->result->error(null);
        }
        $this->status = $status;
    }

    /**
     * Set
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->$name = $value;
    }

    /**
     * Set id
     *
     * @param string|null $id
     * @return void
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get extension name
     *
     * @return string|null
     */
    public function getExtensionName(): ?string 
    {
        return $this->extension;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return (empty($this->name) == true) ? Utils::getBaseClassName($this) : $this->name;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set name
     *
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return void
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Set extension name
     *
     * @param string|null $name
     * @return void
     */
    public function setExtensionName(?string $name): void
    {
        $this->extension = $name;
    }

    /**
     * Set executuion Queue (null for any)
     *
     * @param string|null $name
     * @return void
     */
    public function setQueue(?string $name): void
    {
        $this->queue = $name;
    }

    /**
     * Get queue
     *
     * @return string|null
     */
    public function getQueue(): ?string
    {
        return $this->queue;
    }
}
