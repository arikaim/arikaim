<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Extension;

use Arikaim\Container\Container;
use Arikaim\Core\Interfaces\CacheInterface;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Packages\ModulePackage;
use Arikaim\Core\Db\Model;

/**
 * Modules service locator
 */
class Modules  
{
    /**
     * Http Scheme
     * 
     * @var string
    */
    private static $container;

    /**
     * Get container service
     *
     * @param string $name Service name
     * @param array $arguments Service params
     * @return mixed service
    */
    public static function __callStatic($name, $arguments)
    {    
        $service = null;
        if (Self::$container == null) {
            return null;
        }    
       
        if (Self::$container->has($name) == true) {
            $service = Self::$container->get($name);
        }
        if (isset($arguments[0]) == true) {
            $key = $arguments[0];
            if (is_array($service) == true) {
                return (isset($service[$name]) == true) ? Arrays::getValue($service[$name],$key) : Arrays::getValue($service,$key);                            
            }            
            if (is_object($service) == true) {
                if ($service instanceof CollectionInterface) {
                    return Arrays::getValue($service->toArray(),$key);                  
                }
            }            
        }

        return $service;
    }
    
    /**
     * Check item exists in container
     *
     * @param string $name Item name.
     * @return boolean
    */
    public static function has($name)
    {
        return Self::$container->has($name);
    }

    /**
     * Return service container object.
     *
     * @return object
    */
    public static function getContainer()
    {
        return Self::$container;
    }

    /**
     * Set container
     *
     * @param  Psr\Container\ContainerInterface $container
     * @return void
     */
    public static function setContainer($container)
    {
        Self::$container = $container;
    }

    /**
     * Add module services in container
     *
     * @return void
     */
    public static function init(CacheInterface $cache)
    {
        $container = new Container();

        $modules = $cache->fetch('services.list');
        if (is_array($modules) == false) {
            $modules = Model::Modules()->getPackagesList([
                'type'   => ModulePackage::getTypeId('service'), 
                'status' => 1
            ]);
            $cache->save('services.list',$modules,2);    
        } 
        
        foreach ($modules as $module) {
            $serviceName = $module['service_name'];
            if (empty($serviceName) == false) {
                // add to container
                $container[$serviceName] = function() use($module) {
                    return Factory::createModule($module['name'],$module['class']);
                };
            }
            if ($module['bootable'] == true) {
                $service = $container->get($serviceName);
                if (is_object($service) == true) {
                    $service->boot();
                }
            }           
        }

        Self::setContainer($container);
    }
}
