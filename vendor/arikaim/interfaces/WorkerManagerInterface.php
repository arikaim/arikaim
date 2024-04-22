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
 * Queue worker manager interface
 */
interface WorkerManagerInterface
{     
    /**
     * Run worker
     *    
     * @return boolean
     */
    public function run(): bool;

    /**
     * Return true if worker is running
     *    
     * @return boolean
     */
    public function isRunning(): bool;

    /**
     * Stop worker
     *    
     * @return boolean
     */
    public function stop(): bool;

    /**
     * Get title
     *    
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get description
     *    
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Get worker service details
     *
     * @return array
     */
    public function getDetails(): array;

    /**
     * Get host
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Get port
     *
     * @return string
     */
    public function getPort(): string;
}
