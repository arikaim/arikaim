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
use Closure;

/**
 * Database Model Factory 
*/
class Model 
{   
    /**
     * Instance pool
     *
     * @var array
     */
    private static $instances = [];

    /**
     * Create db model instance
     *
     * @param string $className Base model class name
     * @param string|null $extensionName
     * @param Closure|null $callback
     * @param boolean $showError
     * @throws Exception
     * @return object|null
     */ 
    public static function create(string $className, ?string $extensionName = null, $callback = null, bool $showError = true)
    {         
        $fullClass = ($extensionName == '*') ? $className : Factory::getModelClass($className,$extensionName);
    
        // check in pool
        $instance = Self::$instances[$fullClass] ?? null;
        if (empty($instance) == true) {
            $instance = Factory::createInstance($fullClass);
            Self::$instances[$fullClass] = $instance;
        }

        if (\is_callable($callback) == true) {
            return ($instance != null) ? $callback($instance) : null;
        }
        if ($instance == null && $showError == true) {
            throw new Exception('Not valid db model class: ' . $fullClass,1);
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
    public static function hasAttribute($model, string $name): bool
    {
        return \array_key_exists($name,$model->attributes);
    }

    /**
     * Get sql 
     *
     * @param Builder $builder
     * @return string
     */
    public static function getSql($builder): string
    {
        $sql = \str_replace(['?'],["\'%s\'"],$builder->toSql());
        
        return \vsprintf($sql,$builder->getBindings());     
    }

    /**
     * Get model constant
     *
     * @param string $className
     * @param string $constantName
     * @param string|null $extensionName
     * @return mixed
     */
    public static function getConstant(string $className, string $constantName, ?string $extensionName = null)
    {
        return Factory::getConstant(Self::getFullClassName($className,$extensionName),$constantName);
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
        return Self::create($name,$args[0] ?? null,$args[1] ?? null);
    }
    
    /**
     * Return true if instance is valid model class
     *
     * @param object $instance
     * @return boolean
     */
    public static function isValidModel($instance): bool
    {
        return \is_subclass_of($instance,'Illuminate\\Database\\Eloquent\\Model');
    }
}
