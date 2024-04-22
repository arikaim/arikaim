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

/**
 * Job interface
 */
interface JobInterface
{   
    const STATUS_CREATED   = 0;   
    const STATUS_PENDING   = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_EXECUTED  = 3;
    const STATUS_SUSPENDED = 5;
    const STATUS_ERROR     = 10;

    /**
     * Get hjob params
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * Set job params
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void;
    
    /**
     * Get properties descriptor
     *
     * @return object|null
     */
    public function descriptor(): ?object;

    /**
     * Set execution date
     *   
     * @param int|null $time  timestamp
     * @return void
    */
    public function setDateExecuted(?int $time): void;

    /**
     * Set date pushed in queue
     *   
     * @param int|null $time  timestamp
     * @return void
    */
    public function setDateCreated(?int $time): void;

    /**
     * Get execution timestamp
     *   
     * @return int
    */
    public function getDateExecuted(): ?int;

    /**
     * Get date created timestamp
     *   
     * @return int
    */
    public function getDateCreated(): ?int;

    /**
     * Add error
     *
     * @param string $errorMessage
     * @return void
     */
    public function addError(string $errorMessage): void;

    /**
     * Return true if job is executed successful
     *
     * @return boolean
     */
    public function hasSuccess(): bool;

    /**
     * Get execution errors
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Return unique job id
     *
     * @return string|null
     */ 
    public function getId(): ?string;

    /**
     * Set id
     *
     * @param string|null $id
     * @return void
     */
    public function setId(?string $id): void;

    /**
     * Set job status
     *
     * @param integer $status
     * @return void
    */
    public function setStatus(int $status): void;

    /**
     * Get job status
     *   
     * @return int
    */
    public function getStatus(): int;

    /**
     * Return job priority
     *
     * @return int
    */
    public function getPriority(): int;

    /**
     * Set priority
     *
     * @param integer $priority
     * @return void
     */
    public function setPriority(int $priority): void;

    /**
     * Return job name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set name
     *
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void;

    /**
     * Job code
     *
     * @return void
    */
    public function execute();   
    
    /**
     * Return extension name
     *
     * @return string|null
     */
    public function getExtensionName(): ?string;

    /**
     * Set extension name
     *
     * @param string|null $name
     * @return void
     */
    public function setExtensionName(?string $name): void;

    /**
     * Set executuion Queue (null for any)
     *
     * @param string|null $name
     * @return void
     */
    public function setQueue(?string $name): void;

    /**
     * Get queue
     *
     * @return string|null
     */
    public function getQueue(): ?string;

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array;
}
