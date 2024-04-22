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
use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Interfaces\Job\RecurringJobInterface;
use Arikaim\Core\Queue\Traits\Recurring;

/**
 * Base class for all Recurring jobs
 */
abstract class RecurringJob extends Job implements JobInterface, RecurringJobInterface
{
    use Recurring;
}
