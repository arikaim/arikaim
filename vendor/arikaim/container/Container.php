<?php
/**
 *  Arikaim Container
 *  Dependency injection container component
 *  @link        http://www.arikaim.com
 *  @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license     MIT License
 */
namespace Arikaim\Container;

use Arikaim\Container\Exceptions\ServiceNotFoundException;
use Arikaim\Container\Exceptions\ServiceExistsException;
use Psr\Container\ContainerInterface;

/**
 * Dependency injection container.
 * 
 */
class Container implements ContainerInterface, \ArrayAccess 
{    
    /**
     * Container items
     *
     * @var array
     */
    private $services;

    /**
     * Container constructor
     *
     * @param array $services Container services 
     */
    public function __construct(array $services = null)
    {      
        $this->services = [];    
        if (is_array($this->services) == true) {
            $this->services = $services;
        }
    }

    /**
     * Get container service
     *
     * @param string $id Service id
     * @param mixed|null $default Default value
     * @return mixed|null Service or null if not exist
     */
    public function getItem($id, $default = null)
    {
        return ($this->has($id) == false) ? $default : $this->get($id);
    }

    /**
     * Get service from container
     *
     * @param string $id Srvice id 
     * @throws ServiceNotFoundException;
     * @return mixed Service or null if not exist
     */
    public function get($id)
    {
        if ($this->has($id) == false) {
            throw new ServiceNotFoundException($id); 
        } 

        if (\is_object($this->services[$id]) == false || method_exists($this->services[$id], '__invoke') == false) {
            return $this->services[$id];
        }
        $this->services[$id] = $this->services[$id]($this); 
    
        return $this->services[$id];
    }

    /**
     * Check if service exists in container PSR-11 ContainerInterface
     * 
     * @param string $id Service id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * Add service to container
     *
     * @param string $id Service id 
     * @param mixed $service Service value 
     * @param boolean $replace Replace service if exists 
     * @throws ServiceExistsException If replace is false and  service exists in container
     * @return void
     */
    public function add($id, $service, $replace = false)
    {
        if ($this->has($id) == true && $replace == false) {           
            throw new ServiceExistsException($id);
        }
        $this->services[$id] = $service;
    }

    /**
     * Replace service in container
     *
     * @param string $id Sservice id 
     * @param mixed $service Service value
     * @return void
     */
    public function replace($id,$service)
    {
        return $this->add($id,$service,true);
    }

    /**
     * Remove service from container
     *
     * @param string $id Service id
     * @return void
     */
    public function remove($id)
    {
        if ($this->has($id) == true) {
            unset($this->services[$id]);
        }
    }

    /**
     * Get array with all service id in container
     *
     * @return array
     */
    public function getServicesList()
    {
        return array_keys($this->services);
    }

    /**
     * ArrayAccess interface function
     *
     * @param string $id Service id
     * @return bool
     */
    public function offsetExists($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * ArrayAccess interface function
     *
     * @param string $id Service id
     * @return mixed
     */
    public function offsetGet($id)
    {
        return $this->get($id);
    }

    /**
     * ArrayAccess interface function
     *
     * @param string $id Service id
     * @param mixed Service value
     * @return void
     */
    public function offsetSet($id, $service)
    {
        $this->services[$id] = $service;
    }

    /**
     * ArrayAccess interface function
     *
     * @param string $id Service id
     * @return void
     */
    public function offsetUnset($id)
    {
       $this->remove($id);
    }
}
