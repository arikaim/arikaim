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
 * Job log 
*/
trait JobLog
{  
    /**
     * Log message
     *
     * @var string
     */
    protected $logMessage = 'Job executed';

    /**
     * Log context
     *
     * @var array
     */
    protected $logContext = [];

    /**
     * Get log message
     *
     * @return string
     */
    public function getLogMessage(): string
    {
        return $this->logMessage ?? 'Job executed';
    }

    /**
     * Set log messge
     *
     * @param string $message
     * @return void
     */
    public function setLogMessage(string $message): void
    {
        $this->logMessage = $message;
    }

    /**
     * Set log context
     *
     * @param array $context
     * @return void
     */
    public function setLogContext(array $context): void
    {
        $this->logContext = $context;
    }

    /**
     * Get log context
     *
     * @return array
     */
    public function getLogContext(): array
    {
        return $this->logContext ?? [];
    }
}
