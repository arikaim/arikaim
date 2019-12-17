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

use Arikaim\Core\Queue\Jobs\RecuringJob;
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Interfaces\Job\RecuringJobInterface;
use Arikaim\Core\Interfaces\Job\JobInterface;

/**
 * Cron job
 */
class CronJob extends RecuringJob implements RecuringJobInterface,JobInterface
{
    /**
     * Constructor
     *
     * @param string|null $extension
     * @param string|null $name
     */
    public function __construct($extension = null, $name = null)
    {
        parent::__construct($extension,$name);
        $this->interval = '* * * * *';
    }

    /**
     * Set cron expression
     *
     * @param string $interval
     * @return CronJob
     */
    public function setInterval($interval) {
        $this->setRecuringInterval($interval);

        return $this;
    }

    /**
     * Return true if job i due
     *
     * @return boolean
     */
    public function isDue()
    {
        $date = new DateTime();
        
        return CronExpression::factory($this->interval)->isDue('now',$date->getTimeZoneName());
    } 

    /**
     * Job code
     *
     * @return mixed
     */
    public function execute()
    {
        return false;
    }

    /**
     * Run every minute
     *
     * @param integer|null $minutes
     * @return CronJob
     */
    public function runEveryMinute($minutes = null)
    {
        $this->interval = (empty($minutes) == true) ? "* * * * *" : "*/$minutes * * * *";
        return $this;
    }

    /**
     * Run every hour
     *
     * @return CronJob
     */
    public function runEveryHour()
    {
        $this->interval = '0 * * * *';
        return $this;
    }

    /**
     * Run every day
     *
     * @param string|null $time
     * @return CronJob
     */
    public function runEveryDay($time = null)
    {
        if ($time != null) {
            $tokens = explode(':',$time);
            return $this->resolve(2,(int)$tokens[0])->resolve(1,count($tokens) == 2 ? (int)$tokens[1] : '0');
        }

        $this->interval = '0 0 * * *';
        return $this;
    }

    /**
     * Run every week
     *
     * @return CronJob
     */
    public function runEveryWeek()
    {
        $this->interval = '0 0 * * 0';
        return $this;
    }

    /**
     * Run every month
     *
     * @return CronJob
     */
    public function runEveryMonth()
    {
        $this->interval = '0 0 1 * *';
        return $this;
    }

    /**
     * Run every year
     *
     * @return CronJob
     */
    public function runEveryYear()
    {
        $this->interval = '0 0 1 1 *';
        return $this;
    }

    /**
     * Resolve corn expression helper
     *
     * @param integer $position
     * @param mixed $value
     * @return CronJob
     */
    protected function resolve($position, $value)
    {
        $tokens = explode(' ', $this->interval);
        $tokens[$position - 1] = $value;

        $this->interval = implode(' ', $tokens);
        return $this;
    }
}
