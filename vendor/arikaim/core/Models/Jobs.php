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
use Arikaim\Core\Queue\Jobs\RecuringJob;
use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Interfaces\Job\RecuringJobInterface;
use Arikaim\Core\Interfaces\Job\ScheduledJobInterface;

use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;

/**
 * Jobs database model
 */
class Jobs extends Model implements QueueStorageInterface
{
    use Uuid,
        Find,
        Status,
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
        'extension_name',
        'schedule_time',
        'date_created',
        'date_executed',
        'executed',
    ];
    
    /**
     * Db table name
     *
     * @var string
     */
    protected $table = 'jobs';

    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get attribute mutator for due_date
     *
     * @return integer
     */
    public function getDueDateAttribute()
    {
        if ($this->isRecurring() == true) {
            return (empty($this->recuring_interval) == false) ? RecuringJob::getNextRunDate($this->recuring_interval) : null;
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
    public function isRecurring()
    {
        return (empty($this->recuring_interval) == true) ? false : true;
    }

    /**
     * Return true if job is scheduled
     *
     * @return boolean
     */
    public function isScheduled()
    {
        return (empty($this->schedule_time) == true) ? false : true;
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

        return (is_object($model) == true) ? $model->uuid : false;
    }

    /**
     * Add job
     *
     * @param string|integer $id Job id, uiid or name
     * @param array $data
     * @return boolean
     */
    public function addJob(array $data)
    {
        $model = $this->findByColumn($data['name'],'name');

        if (is_object($model) == true) {
            return false;
        }
        $model = $this->create($data);

        return is_object($model);
    }
    
    /**
     * Delete job
     *
     * @param string|integer $id
     * @return boolean
     */
    public function deleteJob($id)
    {
        $model = $this->findById($id);

        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete jobs
     *
     * @param array $filter
     * @return boolean
     */
    public function deleteJobs($filter = [])
    {
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }

        return $model->delete();
    }

    /**
     * Get job
     *
     * @param string|integer $id Job id, uiid or name
     * @return array|false
     */
    public function getJob($id)
    {
        $model = $this->findById($id);

        if (is_object($model) == false) {
            $model = $this->findByColumn($id,'name');
        }
        
        return (is_object($model) == true) ? $model->toArray() : false;
    }

    /**
     * Return true if job exists
     *
     * @param string|integer $id Job id, uiid
     * @return boolean
     */
    public function hasJob($id)
    {
        $model = $this->findById($id);

        return is_object($model);
    }

    /**
     * Get jobs
     *
     * @param array $filter   
     * @return array
     */
    public function getJobs($filter = [])
    {  
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = ($value == '*') ? $model->whereNotNull($key) : $model->where($key,'=',$value);
        }
        $model = $model->get();

        return (is_object($model) == true) ? $model->toArray() : [];
    }

    /**
     * Update execution status
     *
     * @param string|integer $id
     * @param integer        $status
     * @return boolean
     */
    public function setJobStatus($id, $status)
    {
        $model = $this->findById($id);
        if (is_object($model) == false) {
            return false;
        } 

        return $model->update(['status' => $status]);
    }

    /**
     * Update execution status
     *
     * @param JobInterface $job
     * @return bool
     */
    public function updateExecutionStatus(JobInterface $job)
    {       
        $id = (empty($job->getId()) == true) ? $job->uuid : $job->getId();
        $model = $this->findByIdQuery($id);
    
        if (is_object($model->first()) == false) {
            return false;
        } 
        if ($job instanceof RecuringJobInterface) {
            $info = ['date_executed' => DateTime::toTimestamp()];
        }
        if ($job instanceof ScheduledJobInterface) {
            $info = ['date_executed' => DateTime::toTimestamp(),'status' => $model->first()->COMPLETED()];
        }
        // increment execution counter
        $model->increment('executed');
        $result = $model->update($info);     

        return ($result == null) ? true : $result;
    }

    /**
     * Get next Job
     *
     * @return array|false
     */
    public function getNext()
    {       
        $model = $this
            ->where('status','<>',$this->COMPLETED())
            ->whereNull('schedule_time')
            ->whereNull('recuring_interval')
            ->orderBy('priority','desc')->first();

        return (is_object($model) == false) ? false : $model->toArray();           
    }

    /**
     * Get all jobs due
     * 
     * @return array
     */
    public function getJobsDue()
    {
        $model = $this
            ->where('status','=',$this->ACTIVE())          
            ->where(function($query) {
                $query->where('recuring_interval','<>','')->orWhere('schedule_time','<',DateTime::toTimestamp());
            })->orderBy('priority','desc')->get();
            
        return (is_object($model) == true) ? $model->toArray() : [];
    }
}
