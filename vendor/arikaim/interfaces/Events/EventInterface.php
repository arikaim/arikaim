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
    public function setParameter($name, $value);
    
    /**
     * Return all event parameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * Return event parameter
     *
     * @param string $name
     * @return mixed
     */
    public function getParameter($name);

    /**
     * Return true if parameter exist.
     *
     * @param string $name
     * @return boolean
     */
    public function hasParameter($name);

    /**
     * Stop event propagation
     *
     * @return bool
     */
    public function stopPropagation();

    /**
     * Return true if event propagation is enabled
     *
     * @return boolean
     */
    public function isStopped();

    /**
     * Return event name
     *
     * @return string
     */
    public function getName();
}
