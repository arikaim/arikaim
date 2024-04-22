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

use Cron\CronExpression;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Utils\TimeInterval;

/**
 * Recurring job trait
 */
trait Recurring
{
    /**
     * Recuring interval
     *
     * @var string|null
     */
    protected $interval = null;
    
    /**
     * Get next run date
     *
     * @param string $interval
     * @param int|null $dateLastExecution
     * @return integer|false
     */
    public static function getNextRunDate(string $interval, ?int $dateLastExecution = null)
    {
        $dateLastExecution = empty($dateLastExecution) ? DateTime::getCurrentTimestamp() : $dateLastExecution;       
        $dateTime = DateTime::createFromTimestamp($dateLastExecution);

        if (CronExpression::isValidExpression($interval) == true) {
            return CronExpression::factory($interval)->getNextRunDate($dateTime,0,false,DateTime::getTimeZoneName())->getTimestamp();
        }

        if (TimeInterval::isDurationInverval($interval) == true) {
            $interval = TimeInterval::create($interval);
        
            return $dateTime->add($interval)->getTimestamp();
        }

        return false;
    }
    
    /**
     * Get next run date time timestamp
     *
     * @return integer
     */
    public function getDueDate()
    {       
        $dateExecuted = (empty($this->getDateExecuted()) == true) ? $this->getDateCreated() : $this->getDateExecuted();

        return Self::getNextRunDate($this->interval,$dateExecuted);
    }

    /**
     * RecurringJobInterface implementation function
     *
     * @return string|null
     */
    public function getRecurringInterval(): ?string
    {
        return $this->interval;
    }

    /**
     * Return true if job is recurring
     *
     * @return boolean
     */
    public function isRecurring(): bool
    {
        return (empty($this->interval) == false);
    }

    /**
     * Set recurring interval
     *
     * @param string $interval
     * @return void
     */
    public function setRecurringInterval(string $interval): void
    {
        $this->interval = $interval;
    }
}
