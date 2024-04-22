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
use Arikaim\Core\Interfaces\Job\ScheduledJobInterface;
use Arikaim\Core\Interfaces\Job\JobInterface;

use Arikaim\Core\Queue\Traits\Scheduled;

/**
 * Base class for all scheduled jobs
 */
abstract class ScheduledJob extends Job implements ScheduledJobInterface, JobInterface
{
    use Scheduled;
}
