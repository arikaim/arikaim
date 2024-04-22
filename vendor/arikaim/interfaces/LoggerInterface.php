<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Logger interface
 */
interface LoggerInterface
{   
    /**
     * Add log record
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function log($level, string $message, array $context = []): bool;

    /**
     * Add error log
     *
     * @param string $message
     * @param array $context
     * @return boolean
    */
    public function error(string $message, array $context = []): bool;

    /**
     * Add info log
     *
     * @param string $message
     * @param array $context
     * @return boolean
    */
    public function info(string $message, array $context = []): bool;
}
