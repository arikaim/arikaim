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

use Arikaim\Core\Queue\Jobs\Job;
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Interfaces\Job\ScheduledJobInterface;
use Arikaim\Core\Interfaces\Job\JobInterface;

/**
 * Base class for all scheduled jobs
 */
abstract class ScheduledJob extends Job implements ScheduledJobInterface, JobInterface
{
    /**
     * Scheduled date time (timestamp)
     *
     * @var integer
     */
    protected $scheduleTime;
 
    /**
     * Constructor
     *  
     * @param string $extension
     * @param string|null $name
     */
    public function __construct($extension, $name = null)
    {
        parent::__construct($extension,$name);

        $this->scheduleTime = null;
    }

    /**
     * ScheduledJobInterface implementation
     *
     * @return integer
     */
    public function getScheduleTime()
    {
        return $this->scheduleTime;
    }

    /**
     * Set scheduled time (timestamp)
     *
     * @param integer $date_time
     * @return ScheduledJob
     */
    public function setScheduleTime($timestamp)
    {
        $this->scheduleTime = $timestamp;

        return $this;
    }

    /**
     * Set scheduled time
     *
     * @param string $date
     * @return ScheduledJob
     */
    public function runAt($date)
    {
        return $this->setScheduleTime(DateTime::toTimestamp($date));
    }

    /**
     * Return true if job is due
     *
     * @return boolean
     */
    public function isDue()
    {
        if (empty($this->getScheduleTime()) == true) {
            return false;
        }

        return ($this->scheduleTime < DateTime::getTimestamp()) ? true : false;
    }
}
