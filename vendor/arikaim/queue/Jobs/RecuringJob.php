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

use Cron\CronExpression;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Queue\Jobs\Job;
use Arikaim\Core\Interfaces\Job\RecuringJobInterface;

/**
 * Base class for all Recurring jobs
 */
abstract class RecuringJob extends Job implements RecuringJobInterface
{
    /**
     * Recuring interval
     *
     * @var string|null
     */
    protected $interval;
    
    /**
     * Constructor
     *
     * @param string|null $extension
     * @param string|null $name
     */
    public function __construct($extension = null, $name = null)
    {
        parent::__construct($extension,$name);

        $this->interval = null;
    }

    /**
     * Get next run date
     *
     * @param string $interval
     * @return integer
     */
    public static function getNextRunDate($interval)
    {
        return CronExpression::factory($interval)->getNextRunDate('now',0,false,DateTime::getTimeZoneName())->getTimestamp();
    }
    
    /**
     * Get next run date time timestamp
     *
     * @return integer
     */
    public function getDueDate()
    {
        return Self::getNextRunDate($this->interval);
    }

    /**
     * RecuringJobInterface implementation function
     *
     * @return string
     */
    public function getRecuringInterval()
    {
        return $this->interval;
    }

    /**
     * Set recuring interval
     *
     * @param string $interval
     * @return void
     */
    public function setRecuringInterval($interval)
    {
        $this->interval = $interval;
    }
}
