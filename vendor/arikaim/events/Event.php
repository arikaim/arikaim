<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Events;

use Arikaim\Core\Interfaces\Events\EventInterface;
use Arikaim\Core\Collection\Collection;

/**
 * Base event class
*/
class Event implements EventInterface
{
    /**
     * Event name
     *
     * @var string
     */
    protected $name;

    /**
     * Event parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Event propagation
     *
     * @var boolean
     */
    protected $propagation = false;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params = []) 
    {
        $this->parameters = $params;     
    }

    /**
     * Set event name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {   
        $this->name = $name;
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setop event propagation
     *
     * @return void
     */
    public function stopPropagation(): void
    {
        $this->propagation = true;
    }

    /**
     * Return true if event propagation is disabled
     *
     * @return boolean
     */
    public function isStopped(): bool
    {
        return $this->propagation;
    }

    /**
     * Set event parameter
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Return event parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Return params array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = $this->parameters;
        $result['event_name'] = $this->getName();
        
        return $result;
    }

    /**
     * Return collection object with event params
     *
     * @return \Collection
     */
    public function toCollection()
    {
        return new Collection($this->parameters);
    }

    /**
     * Return parameter
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getParameter(string $name, $default = null) 
    {
        return $this->parameters[$name] ?? $default;         
    }

    /**
     * Return true if parameter exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);      
    }
}
