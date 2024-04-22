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
 * Job recurring interface
 */
interface RecurringJobInterface extends JobInterface
{   
    /**
     * Return recurring interval
     *
     * @return string|null
     */
    public function getRecurringInterval(): ?string;

    /**
     * Set recurring interval
     *
     * @param string $interval
     * @return void
     */
    public function setRecurringInterval(string $interval): void;

    /**
     * Get next run date time timestamp
     *
     * @return integer
     */
    public function getDueDate();

    /**
     * Return true if job is due
     *
     * @return boolean
     */
    public function isDue(): bool;
}
