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
 * Event interface
 */
interface EventInterface
{    
    /**
     * Set event parameter
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParameter(string $name, $value): void;
    
    /**
     * Return all event parameters
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Return event parameter
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getParameter(string $name, $default = null);

    /**
     * Return true if parameter exist.
     *
     * @param string $name
     * @return boolean
     */
    public function hasParameter(string $name);

    /**
     * Stop event propagation
     *
     * @return void
     */
    public function stopPropagation(): void;

    /**
     * Return true if event propagation is enabled
     *
     * @return boolean
     */
    public function isStopped(): bool;

    /**
     * Return event name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set event name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Return params array
     *
     * @return array
     */
    public function toArray(): array;
}
