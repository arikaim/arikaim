<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator;

use Arikaim\Core\Utils\Factory;

/**
 * Filter factory class
 */
class FilterBuilder
{
    /**
     * Create filter 
     *
     * @param string $class
     * @param mixed $args
     * @return Arikaim\Core\Interfaces\FilterInterface
     */
    public static function createFilter($class, $args = null)
    {              
        return Factory::createInstance(Factory::getValidatorFiltersClass($class),$args);             
    }

    /**
     * Create filter
     *
     * @param string $name
     * @param mixed $args
     * @return Arikaim\Core\Validator\Interfaces\FilterInterface
     */
    public static function __callStatic($name, $args)
    {  
        return Self::createFilter(ucfirst($name),$args);       
    }

    /**
     * Create filter 
     *
     * @param string $name
     * @param mixed $args
     * @return Arikaim\Core\Validator\Interfaces\FilterInterface
     */
    public function __call($name, $args)
    {  
        return Self::createFilter(ucfirst($name),$args);       
    }
}
