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

use Arikaim\Core\Collection\Properties;

/**
 * Object descriptior
 */
abstract class AbstractDescriptor 
{
    /**
     * Properties collection
     *
     * @var Properties
     */
    private $properties;

    /**
     * Property collections
     *
     * @var array
     */
    private $collections;

    /**
     * Properties descriptor in child class
     *
     * @return void
     */
    abstract protected function definition(): void;

    /**
     * Constructor
     *
     */
    public function __construct() 
    {
        $this->collections = [];
        $this->properties = new Properties();   

        $this->definition();
    }
   
    /**
     * Create property 
     *
     * @param string $name
     * @param array|object|string|Callable $descriptor
     * @return Properties
     */
    protected function property(string $name, $descriptor)
    {
        return $this->properties->property($name,$descriptor);
    }

    /**
     * Create collection properties
     *
     * @param string $name
     * @return Properties
     */
    protected function createCollection(string $name): Properties
    {
        $this->collections[$name] = new Properties();

        return $this->collections[$name];
    }

    /**
     * Get collection properties
     *
     * @param string $name
     * @return Properties|null
     */
    public function collection(string $name): ?Properties
    {
        return $this->collections[$name] ?? null;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return null|Property|Properties
     */
    public function get(string $name)
    {
        $property = $this->properties->getProperty($name);

        return ($property == null) ? $this->collection($name) : $property;
    }

    /**
     * Get property value
     *
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name)
    {
        return $this->properties->getValue($name);
    }

    /**
     * Set property value
     *
     * @param string $name
     * @param mixed $value
     * @return Self
     */
    public function set(string $name, $value): Self
    {
        $this->properties->setPropertyValue($name,$value);

        return $this;
    }

    /**
     * Get properties values
     *
     * @return array
     */
    public function getValues(): array
    {
        $result = $this->properties->getValues();

        foreach ($this->collections as $key => $properties) {
            $result[$key] = $properties->getValues();
        }

        return $result;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = $this->properties->toArray();
        
        foreach ($this->collections as $key => $properties) {
            $result[$key] = $properties->toArray();
        }

        return $result;
    }
}
