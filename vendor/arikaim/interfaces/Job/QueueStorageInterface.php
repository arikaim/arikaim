<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Job;

/**
 * Job recuring interface
 */
interface QueueStorageInterface
{
    /**
     * Update execution status
     *
     * @param string|integer $id
     * @param integer        $status
     * @return boolean
     */
    public function setJobStatus($id, $status);

    /**
     * Update execution status
     *
     * @param JobInterface $job
     * @return bool
    */
    public function updateExecutionStatus(JobInterface $job);

    /**
     * Add job
     *
     * @param array $data
     * @return boolean
     */
    public function addJob(array $data);
    
    /**
     * Return true if job exists
     *
     * @param string|integer $id
     * @return boolean
     */
    public function hasJob($id);

    /**
     * Get job
     *
     * @param string|integer $id
     * @return array|false
     */
    public function getJob($id);

    /**
     * Find job and return job id
     *
     * @param array $filter
     * @return string|false
     */
    public function getJobId(array $filter = []);

    /**
     * Delete job
     *
     * @param string|integer $id
     * @return boolean
     */
    public function deleteJob($id);

    /**
     * Delete jobs
     *
     * @param array $filter
     * @return boolean
     */
    public function deleteJobs($filter = []);

    /**
     * Get jobs
     *
     * @param array $filter   
     * @return array
     */
    public function getJobs($filter = []);

    /**
     * Get all jobs due
     * 
     * @return array
     */
    public function getJobsDue();

    /**
     * Get next Job
     *
     * @return array|false
     */
    public function getNext();
}
