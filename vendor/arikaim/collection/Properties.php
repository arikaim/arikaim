<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Collection;

use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Collection\Property;

/**
 * Properties collection
 */
class Properties extends Collection
{ 
    /**
     * Properties array
     *
     * @var array
     */
    private $properties;

    /**
     * Constructor
     * 
     * @param boolean $resolveProperties
     * @param array $data
     */
    public function __construct($data = [], $resolveProperties = true) 
    {
        $this->properties = [];

        if ($resolveProperties == true) {
            $this->createProperties($data);
        } else {
            parent::__construct($data);
        }
    }

    /**
     * Set property 
     *
     * @param string $name
     * @param array|object|string|Callable $descriptor
     * @return Properties
     */
    public function property($name, $descriptor)
    {
        if (is_array($descriptor) == true) {
            $this->data[$name] = Property::create($descriptor);
        }
        if (is_object($descriptor) == true) {
            $this->data[$name] = $descriptor;
        }
        if (is_string($descriptor) == true) {
            $this->data[$name] = Property::createFromText($descriptor);
        }
        if (is_callable($descriptor) == true) {
            $property = new Property($name);
            $callback = function() use($property,$descriptor) {
                $descriptor($property);
                return $property;
            };
            $this->data[$name] = $callback();
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface
     */
    public function getProperty($name)
    {
        return (isset($this->data[$name]) == true) ? $this->data[$name] : new Property($name);
    }

    /**
     * Get properties, return Property objects array
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->data;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {       
        $callback = function($value, $key) {
            $property =  $this->getProperty($key);
            $this->properties[$key] = $property->toArray();           
        };
        array_walk_recursive($this->data ,$callback);

        return $this->properties;
    }

    /**
     * Get property value
     *
     * @param string $key
     * @return mixed
     */
    public function getValue($key)
    {
        return $this->getProperty($key)->getValue();       
    }

    /**
     * Get properties list
     *
     * @param boolean|null $editable
     * @param boolean|null $hidden
     * @return array
     */
    public function gePropertiesList($editable = null, $hidden = null)
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            $property = $this->getProperty($key);

            if ($editable == true) {
                if ($property->isReadonly() == false && $property->isHidden() == false) {
                    $result[$key] = $property;
                }  
               
            }                
            if ($editable == false) {
                if ($property->isReadonly() == true || $property->isHidden() == true) {
                    $result[$key] = $property;
                }                 
            }

            if (empty($hidden) == false) {
                if ($property->isHidden() == $hidden) {
                    $result[$key] = $property;
                } else {
                    unset($result[$key]);
                }
            }
        }    

        return $result;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            $result[$key] = $this->getProperty($key)->getValue();          
        }    

        return $result;
    }

    /**
     * Set value of every property from data array
     *
     * @param array $data
     * @return void
     */
    public function setPropertyValues(array $data)
    {
        foreach ($data as $key => $value) {
            $this->getProperty($key)->value($value);
        }
    }

    /**
     * Replaces data array with properties array for every item in data array.
     *
     * @param array $data
     * @return boolean
     */
    private function createProperties(array $data)
    {
        $this->data = [];      
        $callback = function($value, $key) {
            $property = new Property($key);
            $this->data[$key] = $property;
        };
        
        return array_walk_recursive($data,$callback);       
    }
}
