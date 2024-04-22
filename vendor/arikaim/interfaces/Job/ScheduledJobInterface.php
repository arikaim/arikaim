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

use Arikaim\Core\Interfaces\Job\JobInterface;

/**
 * Scheduled job interface
 */
interface ScheduledJobInterface extends JobInterface
{   
    /**
     * Return schduled time (timestamp)
     *
     * @return integer|null
     */
    public function getScheduleTime(): ?int;

    /**
     * Set schedule time
     *
     * @param integer $timestamp
     * @return mixed
     */
    public function setScheduleTime(int $timestamp);
    
    /**
     * Return true if job is due
     *
     * @return boolean
     */
    public function isDue(): bool;
}
