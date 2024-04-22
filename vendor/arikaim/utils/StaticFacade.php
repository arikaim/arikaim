<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Utils;

use Arikaim\Core\Utils\Utils;

/**
 * Facades abstract class
 */
abstract class StaticFacade 
{
    /**
     * Class instance
     *
     * @var object|null
     */
    static protected $instance;

    /**
     * Return class name used form facade
     *
     * @return string
     */    
    abstract public static function getInstanceClass();

    /**
     * Get instance args
     *
     * @return mixed
     */
    public static function getInstanceArgs()
    {
        return null;
    }

    /**
     * Create instance
     *
     * @return object|null
     */
    private static function createInstance()
    {
        $class = static::getInstanceClass();
        $args = static::getInstanceArgs();

        if (\class_exists($class) == true) {
            return ($args != null) ? new $class(...$args) : new $class();  
        }
        
        return null;
    }

    /**
     * Get instance
     *
     * @return object|null
     */
    public static function getInstance()
    {
        static::$instance = (\is_object(static::$instance) == false) ? static::createInstance() : static::$instance;

        return static::$instance;
    }

    /**
     * Call methods on instance as static
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     * 
     * @throws RuntimeException
     */
    public static function __callStatic($method, $args)
    {       
        $instance = static::getInstance();
        if (\is_object($instance) == false) {        
            throw new \RuntimeException('Facade instance not set.');
        }
        
        return Utils::call($instance,$method,$args);
    }
}
