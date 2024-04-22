<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Interfaces\Job\QueueStorageInterface;
use Arikaim\Core\Queue\Jobs\RecurringJob;
use Arikaim\Core\Interfaces\Job\JobInterface;

use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\OptionsAttribute;
use Arikaim\Core\Db\Traits\UserRelation;

/**
 * Queue database model
 */
class Queue extends Model implements QueueStorageInterface
{
    use Uuid,
        Find,
        Status,
        OptionsAttribute,
        UserRelation,
        DateCreated;
 
    /**
     * Fillable attributes
     *
     * @var array
    */
    protected $fillable = [
        'name',
        'priority',
        'recuring_interval',
        'handler_class',      
        'status',
        'user_id',
        'extension_name',
        'schedule_time',
        'date_created',
        'date_executed',
        'executed',
        'config',
        'type',
        'service_name',
        'queue'
    ];
    
    /**
     * Options column name
     *
     * @var string
     */
    protected $optionsColumnName = 'config';

    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'queue';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Append custom attributes
     *
     * @var array
     */
    public $appends = [
        'due_date',
        'options'
    ];

    /**
     * Update job config param
     *
     * @param string|int $id Job id
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function saveJobConfigParam($id, string $key, $value): bool
    {
        $model = $this->findById($id);
       
        return ($model == null) ? false : $model->saveOption($key,$value);
    }

    /**
     * Get attribute mutator for due_date
     *
     * @return integer|null
     */
    public function getDueDateAttribute()
    {
        if ($this->isRecurring() == true) {
            return (empty($this->recuring_interval) == false) ? RecurringJob::getNextRunDate($this->recuring_interval,$this->date_executed) : null;
        }
        if ($this->isScheduled() == true) {
            return $this->schedule_time;
        }
    }

    /**
     * Return true if job is recurring 
     *
     * @return boolean
     */
    public function isRecurring(): bool
    {
        return (empty($this->recuring_interval) == false);
    }

    /**
     * Return true if job is scheduled
     *
     * @return boolean
     */
    public function isScheduled(): bool
    {
        return (empty($this->schedule_time) == false);
    }

    /**
     * Find job and return job id
     *
     * @param array $filter
     * @return string|false
     */
    public function getJobId(array $filter = [])
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }
        $model = $model->first();

        return ($model !== null) ? $model->uuid : false;
    }

    /**
     * Add job
     *
     * @param array $data
     * @return boolean
     */
    public function addJob(array $data): bool
    {
        return ($this->create($data) != null);
    }
    
    /**
     * Delete job
     *
     * @param string|integer $id
     * @return boolean
     */
    public function deleteJob($id): bool
    {
        $model = $this->findById($id);

        return ($model != null) ? (bool)$model->delete() : false;
    }

    /**
     * Delete jobs
     *
     * @param array $filter
     * @return boolean
     */
    public function deleteJobs(array $filter = []): bool
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return ($model->delete() !== false);
    }

    /**
     * Get job
     *
     * @param string|integer $id Job id or uiid
     * @return array|null
     */
    public function getJob($id): ?array
    {
        $model = $this->findById($id);
       
        return ($model != null) ? $model->toArray() : null;
    }

    /**
     * Return true if job exists
     *
     * @param string|integer $id Job id, uiid
     * @return boolean
     */
    public function hasJob($id): bool
    {  
        return ($this->findById($id) != null);
    }

    /**
     * Get jobs
     *
     * @param array $filter   
     * @return array
     */
    public function getJobs(array $filter = []): ?array
    {  
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return $model->get()->toArray();
    }

    /**
     * Update execution status
     *
     * @param string|integer $id
     * @param integer $status
     * @return boolean
     */
    public function setJobStatus($id, int $status): bool
    {
        $model = $this->findById($id);

        return ($model == null) ? false : (bool)$model->update(['status' => $status]);
    }

    /**
     * Update execution status
     *
     * @param JobInterface $job
     * @return bool
     */
    public function updateExecutionStatus(JobInterface $job): bool
    {              
        $model = $this->findByIdQuery($job->getId());
    
        if ($model->first() == null) {
            return false;
        } 
       
        $status = ($job->isRecurring() == true) ? JobInterface::STATUS_EXECUTED : JobInterface::STATUS_COMPLETED;
    
        // increment execution counter
        $model->increment('executed');

        return (bool)$model->update([
            'date_executed' => DateTime::toTimestamp(),
            'status'        => $status
        ]);            
    }

    /**
     * Get next Job
     *
     * @return array|null
     */
    public function getNext(): ?array
    {               
        $model = $this->getJobsDueQuery()->orderBy('priority','desc')->first();

        return ($model != null) ? $model->toArray() : null;           
    }

    /**
     * Get all jobs due or filter by name
     * 
     * @param string|null $jobName
     * @return array|null
     */
    public function getJobsDue(?string $jobName = null): ?array
    {
        return $this->getJobsDueQuery($jobName)
            ->orderBy('priority','desc')
            ->get()
            ->toArray();       
    }

    /**
     * Get jobs due query
     *
     * @param string|null $jobName
     * @return Builder
     */
    public function getJobsDueQuery(?string $jobName = null)
    {
        $query = $this
            ->where('status','<>',JobInterface::STATUS_COMPLETED)        
            ->where('status','<>',JobInterface::STATUS_ERROR)      
            ->where('status','<>',JobInterface::STATUS_SUSPENDED)          
            ->where(function($query) {
                // scheduled
                $query->whereNull('schedule_time')->orWhere('schedule_time','<',DateTime::getCurrentTimestamp());
                // recurring
                $query->orWhere(function($query) {
                    $query->where('recuring_interval','<>','');
                });
            });

        return (empty($jobName) == false) ? $query->where('name','=',$jobName) : $query;
    }
}
