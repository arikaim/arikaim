<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

use Arikaim\Core\Utils\Factory;
use Exception;

/**
 * Database Model Factory 
*/
class Model 
{   
    /**
     * Create db model instance
     *
     * @param string $className Base model class name
     * @param string $extensionName
     * @param Closure|null $callback
     * @throws Exception
     * @return object|null
     */ 
    public static function create($className, $extensionName = null, $callback = null) 
    {         
        $fullClass = (class_exists($className) == false) ? Factory::getModelClass($className,$extensionName) : $className; 
        $instance = Factory::createInstance($fullClass);

        if (is_callable($callback) == true) {
            return $callback($instance);
        }
        if (is_object($instance) == false){
            throw new Exception("Not valid db model class: $fullClass", 1);
        }
        
        return $instance;
    }

    /**
     * Return true if attribute exist
     *
     * @param string $name
     * @param Model $model
     * @return boolean
     */
    public static function hasAttribute($model, $name)
    {
        return array_key_exists($name, $model->attributes);
    }

    /**
     * Get sql 
     *
     * @param Builder|Model $builder
     * @return string
     */
    public static function getSql($builder)
    {
        $sql = str_replace(array('?'), array('\'%s\''),$builder->toSql());
        return vsprintf($sql,$builder->getBindings());     
    }

    /**
     * Get model constant
     *
     * @param string $className
     * @param string $constantName
     * @param string $extensionName
     * @return mixed
     */
    public static function getConstant($className, $constantName, $extensionName = null)
    {
        $className = Self::getFullClassName($className,$extensionName);
        return Factory::getConstant($className,$constantName);
    }

    /**
     * Create model
     *
     * @param string $name
     * @param array $args
     * @return object|null
     */
    public static function __callStatic($name, $args)
    {  
        $extensionName = (isset($args[0]) == true) ? $args[0] : null;
        $callback = (isset($args[1]) == true) ? $args[1] : null;

        return Self::create($name,$extensionName,$callback);
    }
    
    /**
     * Return true if instance is valid model class
     *
     * @param object $instance
     * @return boolean
     */
    public static function isValidModel($instance)
    {
        return is_subclass_of($instance,"Illuminate\\Database\\Eloquent\\Model");
    }
}
