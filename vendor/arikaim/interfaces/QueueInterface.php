<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

use Arikaim\Core\Interfaces\Job\JobInterface;

/**
 * Queue interface
 */
interface QueueInterface
{    
    /**
     * Add job
     *
     * @param JobInterface $job
     * @param string|null $extension
     * @return bool
    */
    public function addJob(JobInterface $job, $extension = null);
    
    /**
     * Delete job
     *
     * @param string|integer $id Job id, uiid
     * @return bool
     */
    public function deleteJob($id);
    
    /**
     * Delete all jobs
     *    
     * @return boolean
     */
    public function clear();

    /**
     * Get next job
     *
     * @return JobInterface|null
     */
    public function getNext();

    /**
     * Run job
     *
     * @param JobInterface|string|integer $job
     * @return boolean
     */
    public function executeJob($job);

    /**
     * Get all jobs due
     * 
     * @return array
     */
    public function getJobsDue();

    /**
     * Get jobs
     *
     * @param array $filter
     * @return array
     */
    public function getJobs($filter = []);

    /**
     * Get recurring jobs
     *
     * @param string|null $extension
     * @return array
     */
    public function getRecuringJobs($extension = null);

    /**
     * Create job obj from jobs queue
     *
     * @param string|integer $name
     * @return JobInterface|false
     */
    public function create($name);

    /**
     * Delete jobs
     *
     * @param array $filter
     * @return boolean
     */
    public function deleteJobs($filter = []);
}
