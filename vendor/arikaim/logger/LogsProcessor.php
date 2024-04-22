<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Logger;

/**
 * Log processor class
 */
class LogsProcessor
{    
    /**
     * Add current time in log record
     *
     * @param array $record
     * @return void
     */
    public function __invoke(array $record)
    {
        $record['timestamp'] = time();
        
        return $record;
    }
}
