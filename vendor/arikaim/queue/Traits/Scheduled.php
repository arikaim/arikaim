<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Queue\Traits;

use Arikaim\Core\Utils\DateTime;

/**
 * Scheduled job trait
 */
trait Scheduled
{
    /**
     * Scheduled date time (timestamp)
     *
     * @var integer|null
     */
    protected $scheduleTime = null;
 
    /**
     * ScheduledJobInterface implementation
     *
     * @return integer|null
     */
    public function getScheduleTime(): ?int
    {
        return $this->scheduleTime;
    }

    /**
     * Set scheduled time (timestamp)
     *
     * @param integer $timestamp
     * @return object
     */
    public function setScheduleTime(int $timestamp)
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
    public function runAt(string $date)
    {
        return $this->setScheduleTime(DateTime::toTimestamp($date));
    }
}
