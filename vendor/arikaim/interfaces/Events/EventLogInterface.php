<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Events;

/**
 * Event log interface
 */
interface EventLogInterface
{   
    /**
     * Get log message
     *
     * @return string
     */
    public function getLogMessage(): string;

    /**
     * Set log messge
     *
     * @param string $message
     * @return void
    */
    public function setLogMessage(string $message): void;
    
    /**
     * Get log context
     *
     * @return array
     */
    public function getLogContext(): array;

    /**
     * Set log context
     *
     * @param array $context
     * @return void
    */
    public function setLogContext(array $context): void;
}
