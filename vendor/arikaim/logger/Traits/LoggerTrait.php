<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Logger\Traits;

use Arikaim\Core\Interfaces\LoggerInterface;

/**
 * Logger trait 
*/
trait LoggerTrait
{  
    /**
     * Logger ref
     *
     * @var LoggerInterface|null
     */
    protected $logger = null;

    /**
     * Get logger
     *
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Set logger
     *
     * @param LoggerInterface|null $logger
     * @return void
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Log info messsage
     *
     * @param string $message
     * @param array $context
     * @return boolean
    */
    public function Loginfo(string $message, array $context = []): bool
    {
        return (empty($this->logger) == false) ? $this->logger->info($message,$context) : false;
    }

    /**
     * Log error message
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function LogError(string $message, array $context = []): bool
    {    
        return (empty($this->logger) == false) ? $this->logger->error($message,$context) : false;             
    }
}
