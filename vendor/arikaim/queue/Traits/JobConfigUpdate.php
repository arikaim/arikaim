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

/**
 * Job config params update in queue 
*/
trait JobConfigUpdate
{
    /**
     * Update job config parma value in queue
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function updateParam(string $key, $value): bool
    {
        global $arikaim;

        return $arikaim->get('queue')->updateJobParam($this->getId(),$key,$value);
    }
}
