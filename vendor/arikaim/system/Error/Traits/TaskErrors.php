<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error\Traits;

/**
 * Task Errors trait
 */
trait TaskErrors  
{
    /**
     * Task errors
     *
     * @var array
     */
    protected $taskErrors = [];

    /**
     * Get error
     *
     * @param integer $index
     * @return mixed
     */
    public function getError(int $index = 0)
    {
        return $this->taskErrors[$index] ?? null; 
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->taskErrors ?? [];
    }

    /**
     * Return true if have error
     *
     * @return boolean
     */
    public function hasError(): bool
    {
        return (count($this->getErrors()) > 0);
    }

    /**
     * Add error
     *
     * @param string $errorMessage
     * @return void
     */
    public function addError(string $errorMessage): void
    {
        $this->taskErrors[] = $errorMessage;
    }

    /**
     * Clear Error
     *
     * @return void
     */
    public function clearErrors(): void
    {
        $this->taskErrors = [];
    }
}
