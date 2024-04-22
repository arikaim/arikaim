<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Driver;

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Collection\Properties;
use Arikaim\Core\Collection\PropertiesFactory;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Interfaces\Driver\DriverRegistryInterface;
use Arikaim\Core\Interfaces\Driver\DriverManagerInterface;

/**
 * Driver manager
*/
class DriverManager implements DriverManagerInterface
{
    /**
     * Driver registry adapter
     *
     * @var DriverRegistryInterface
     */
    protected $driverRegistry;

    /**
     * Constructor
     * 
     * @param DriverRegistryInterface $driverRegistry
     */
    public function __construct(DriverRegistryInterface $driverRegistry)
    {
        $this->driverRegistry = $driverRegistry;
    }

    /**
     * Create driver
     *
     * @param string $name Driver name 
     * @param array $options  
     * @param array|null $config Drievr config properties
     * @return DriverInterface|null
     */
    public function create(string $name, array $options = [], ?array $config = null): ?object
    {       
        $driverInfo = $this->driverRegistry->getDriver($name);
        if ($driverInfo === false) {          
            return null;
        }
      
        $properties = PropertiesFactory::createFromArray($config ?? $driverInfo['config']); 
        $driver = Factory::createInstance($driverInfo['class']); 

        if ($driver instanceof DriverInterface) {
            $driver->setDriverOptions($options);  
            $driver->setDriverConfig($properties->getValues());             
            $driver->initDriver($properties);  
            
            return $driver;
        } 

        return null;
    }

    /**
      * Install driver
      *
      * @param string|object $name Driver name
      * @param string|null $class full class name or driver object ref
      * @param string|null $category
      * @param string|null $title
      * @param string|null $description
      * @param string|null $version
      * @param array $config
      * @param string|null $extension
      * @return boolean
    */
    public function install(
        $name, 
        ?string $class = null,
        ?string $category = null,
        ?string $title = null,
        ?string $description = null,
        ?string $version = null,
        array $config = [],
        ?string $packageName = null,
        ?string $packageType = null
    ): bool
    {      
        $info = $this->getDriverParams($name);

        if ($info == null) {
            $info = [
                'name'           => $name,
                'category'       => $category,
                'title'          => $title,
                'class'          => $class,
                'description'    => $description,
                'version'        => $version ?? '1.0.0',               
                'config'         => $config
            ];
        }

        if ($packageType == 'module') {
            $info['module_name'] = $packageName;
        } else {
            $info['extension_name'] = $packageName;
        }
        
        return $this->driverRegistry->addDriver($info['name'],$info);
    }

    /**
     * Get driver params
     *
     * @param string|object $driver Driver obj ref or driver class
     * @return array|null
     */
    protected function getDriverParams($driver): ?array
    {
        $driver = (\is_string($driver) == true) ? Factory::createInstance($driver) : $driver;   
        if ($driver == null) {
            return null;
        }

        $properties = new Properties([]);   
        
        $callback = function() use($driver,$properties) {
            $driver->createDriverConfig($properties);           
            return $properties;
        };
      
        return [
            'name'        => $driver->getDriverName(),
            'category'    => $driver->getDriverCategory(),
            'title'       => $driver->getDriverTitle(),
            'class'       => $driver->getDriverClass(),
            'description' => $driver->getDriverDescription(),
            'version'     => $driver->getDriverVersion(),
            'config'      => $callback()->toArray()
        ];        
    }

    /**
     * Uninstall driver
     *
     * @param string $name Driver name   
     * @return boolean
     */
    public function unInstall(string $name): bool
    {
        return $this->driverRegistry->removeDriver($name);       
    }
    
    /**
     * Return true if driver exsits
     *
     * @param string $name Driver name
     * @return boolean
     */
    public function has(string $name): bool
    {
        return $this->driverRegistry->hasDriver($name);
    }

    /**
     * Get driver
     *
     * @param string $name Driver name
     * @return object|false
     */
    public function getDriver(string $name)
    {
        return $this->driverRegistry->getDriver($name);
    }

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array|object $config
     * @return boolean
     */
    public function saveConfig(string $name, $config): bool
    {            
        $config = (\is_object($config) == true) ? $config->toArray() : $config;

        return $this->driverRegistry->saveConfig($name,$config);
    }

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return Properties
     */
    public function getConfig(string $name)
    {
        $config = $this->driverRegistry->getDriverConfig($name);
        
        return PropertiesFactory::createFromArray($config);         
    }

    /**
     * Get drivers list
     *
     * @param string|null   $category
     * @param integer|null  $status
     * @return array
     */
    public function getList(?string $category = null, ?int $status = null): array
    {
        return $this->driverRegistry->getDriversList($category,$status);
    }

    /**
     * Enable driver
     *
     * @param string $name
     * @return boolean
     */
    public function enable(string $name): bool
    {
        return $this->driverRegistry->setDriverStatus($name,1);
    }

    /**
     * Disable driver
     *
     * @param string $name
     * @return boolean
     */
    public function disable(string $name): bool
    {
        return $this->driverRegistry->setDriverStatus($name,0);
    }
}
