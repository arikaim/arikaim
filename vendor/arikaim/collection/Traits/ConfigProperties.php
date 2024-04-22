<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Traits;

use Arikaim\Core\Collection\Properties;
use Arikaim\Core\Collection\PropertiesFactory;

/**
 * Config properties 
*/
trait ConfigProperties 
{  
    /**
     * Config properties collection
     *
     * @var Properties|null
     */
    protected $configProperties = null;

    /**
     * Create config properties array
     *    
     * @param array|null $values
     * @return array
     */
    public function createConfigProperties(?array $values = null): array
    {
        $properties = new Properties([],false);   
        $callback = function() use($properties,$values) {
            $this->initConfigProperties($properties);  
            if (\is_array($values) == true) {
               $properties->setPropertyValues($values);
            }         
            return $properties;
        };
      
        return $callback()->toArray();     
    }
 
    /**
     * Get config properties collection
     *
     * @param array|null $config
     * @return Properties
     */
    public function getConfigProperties(?array $config = null): Properties
    {
        return (empty($this->configProperties) == true) ? new Properties($config ?? [],false) : $this->configProperties;
    }

    /**
     * Set property value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setPropertyValue(string $key, $value): void
    {
        $this->configProperties->setPropertyValue($key,$value);
    }

    /**
     * Get config properties collection
     *
     * @param Properties|array|string $properties
     * @return void
     */
    public function setConfigProperties($properties): void
    {
        if (\is_string($properties) == true) {
            $properties = \json_decode($properties,true);
        }
        if (\is_array($properties) == true) {
            $properties = PropertiesFactory::createFromArray($properties); 
        }
       
        $this->configProperties = $properties;
    }
}
