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
    public function setJobStatus($id, int $status): bool;

    /**
     * Update execution status
     *
     * @param JobInterface $job
     * @return bool
    */
    public function updateExecutionStatus(JobInterface $job): bool;

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
    public function hasJob($id): bool;

    /**
     * Get job
     *
     * @param string|integer $id
     * @return array|null
     */
    public function getJob($id): ?array;

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
    public function deleteJobs(array $filter = []): bool;

    /**
     * Get jobs
     *
     * @param array $filter   
     * @return array
     */
    public function getJobs(array $filter = []): ?array;

    /**
     * Get all jobs due
     * 
     * @param string|null $jobName
     * @return array|null
     */
    public function getJobsDue(?string $jobName = null): ?array;

    /**
     * Get next Job
     *
     * @return array|null
     */
    public function getNext(): ?array;
}
