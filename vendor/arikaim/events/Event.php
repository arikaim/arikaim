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
    public function __construct($params = []) 
    {
        $this->parameters = (is_array($params) == true) ? $params : [$params];          
    }

    /**
     * Set event name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {   
        $this->name = $name;
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setop event propagation
     *
     * @return void
     */
    public function stopPropagation()
    {
        $this->propagation = true;
    }

    /**
     * Return true if event propagation is disabled
     *
     * @return boolean
     */
    public function isStopped()
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
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Return event parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Return params array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->parameters;
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
     * @return mxied
     */
    public function getParameter($name) 
    {
        return (isset($this->parameters[$name]) == true) ? $this->parameters[$name] : null;         
    }

    /**
     * Return true if parameter exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasParameter($name)
    {
        return (isset($this->parameters[$name]) == true) ? true : false;           
    }
}
