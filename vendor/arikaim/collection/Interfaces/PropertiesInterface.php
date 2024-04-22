<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Collection\Interfaces;

/**
 * Properties collection interface
 */
interface PropertiesInterface
{    
    /**
     * Set property 
     *
     * @param string $name
     * @param array|object|string|Callable $descriptor
     * @return Properties
     */
    public function property(string $name, $descriptor);

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface|null
     */
    public function getProperty(string $name): ?PropertyInterface;

    /**
     * Get properties, return Property objects array
     *
     * @return array
     */
    public function getProperties(): array;

    /**
     * Set property value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setPropertyValue(string $key, $value): bool;
}
