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
use Arikaim\Core\Collection\Interfaces\PropertyInterface;
use Arikaim\Core\Collection\Interfaces\CollectionInterface;

/**
 * Properties collection
 */
class Properties extends Collection implements CollectionInterface
{ 
    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct(array $data = []) 
    {
        parent::__construct($data);
    }

    /**
     * Create property 
     *
     * @param string $name
     * @param array|object|string|Callable $descriptor
     * @return Properties
     */
    public function property(string $name, $descriptor)
    {
        if (\is_array($descriptor) == true) {
            $descriptor['name'] = $descriptor['name'] ?? $name;
            $property = Property::create($descriptor);
        }
        if (\is_object($descriptor) == true) {
            $property = $descriptor;
        }
        if (\is_string($descriptor) == true) {
            $property = Property::createFromText($descriptor);
        }
        if (\is_callable($descriptor) == true) {
            $property = new Property($name);
            $callback = function() use($property,$descriptor) {
                $descriptor($property);
                return $property;
            };
            $property = $callback();          
        }
      
        $group = $property->getGroup();
        
        if ($property->isGroup() == true) {
            $this->add('groups',$property->getValue());
        }
        if (empty($group) == false) {           
            $this->data[$group][$name] = $property->toArray();
        } else {           
            $this->data[$name] = $property->toArray();
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface|null
     */
    public function getProperty(string $name): ?PropertyInterface
    {
        $propertyData = $this->data[$name] ?? null;
        if (empty($propertyData) == true) {
            return null;
        }
        if (\is_array($propertyData) == true) {
            return Property::create($propertyData);
        }
        if ($propertyData instanceof PropertyInterface) {
            return $propertyData;   
        }
        
        return null;
    }

    /**
     * Get properties, return Property objects array
     *
     * @return array
     */
    public function getProperties(): array
    {
        return $this->data;
    }

    /**
     * Get property value
     *
     * @param string $key
     * @param string|null $group
     * @return mixed
     */
    public function getValue(string $key, ?string $group = null)
    {
        $property = (empty($group) == true) ? $this->get($key) : $this->data[$group][$key] ?? [];
        $default = $property['default'] ?? null;
        $type = $property['type'] ?? null;

        if ($type == PropertyInterface::BOOLEAN_TYPE) {
            return $property['value'] ?? $default;
        }
        $value = \trim($property['value'] ?? '');

        return (empty($value) == true) ? \trim($default ?? '') : $value;
    }

     /**
     * Get property value
     *
     * @param string $key
     * @param string|null $group
     * @return string
     */
    public function getValueAsText(string $key, ?string $group = null): string
    {
        $value = $this->getValue($key,$group);
        $type = $this->getType($key,$group);

        switch($type) {
            case PropertyInterface::BOOLEAN_TYPE:              
                return (empty($value) == true || $value == 0 || $value == '0') ? 'false' : 'true';
            break;
        }
     
        return (string)$value ?? '';
    }

    /**
     * Get property type
     *
     * @param string $key
     * @param string|null $group
     * @return int|null
     */
    public function getType(string $key, ?string $group = null): ?int
    {
        if ($this->has($key) == false) {
            return null;
        }
        $property = (empty($group) == true) ? $this->get($key) : $this->data[$group][$key];

        return $property['type'] ?? null;        
    }

    /**
     * Get groups
     *
     * @return array
     */
    public function getGroups(): array 
    {
        $result = [];
        foreach ($this->data as $key => $property) {
            if (isset($property['type']) == true) {
                if ($property['type'] == PropertyInterface::GROUP) {
                    $result[] = $property;
                }              
            }
        }    

        return $result;
    }

    /**
     * Get properties list
     *
     * @param boolean $readonly
     * @param boolean $hidden
     * @param string|null $group
     * @return array
     */
    public function getPropertiesList(bool $readonly = false, bool $hidden = false, ?string $group = null): array
    {
        $result = [];
        $data = (empty($group) == false) ? $this->data[$group] : $this->data;
        $groups = $this->get('groups',[]);

        foreach ($data as $item) {     
            if (empty($group) == false && (\is_array($item) == false || $item == 'items')) {
                continue;
            }
            if ($item == 'groups') {                
                continue;
            }
            if (\in_array($item,$groups) === true && empty($groups) == false) {                               
                continue;
            }
        
            $property = Property::create($item);                      
            if ($property == null) {                         
                continue;
            }
          
            if ($property->isGroup() == true) {
                continue;
            }

            if (empty($group) == false && $property->getGroup() != $group) {                  
                continue;
            }

            if ($readonly == true && $property->isReadonly() == false) {              
                continue;                                                               
            }

            if ($readonly == false && $property->isReadonly() == true) {              
                continue;                                                               
            }

            if ($hidden == true && $property->isHidden() == false) {
                continue;              
            }

            if ($hidden == false && $property->isHidden() == true) {
                continue;              
            }

            // add item
            $result[] = $property->toArray();
        }    

        return $result;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues(): array
    {
        $result = [];
      
        foreach ($this->data as $key => $property) {
            if ($key == 'groups') {
                continue; 
            }
            $type = $property['type'] ?? null;

            if ($type == PropertyInterface::GROUP) {
                foreach ($property as $name => $item) {
                    if ($name == 'items' || \is_array($item) == false) {
                        continue;
                    }
                    $default = $item['default'] ?? null;
                    $value = (empty($item['value']) == true) ? $default : $item['value'];
                    $result[$name] = $value;
                }
            }
            $propertyValue = $property['value'] ?? null;
            $default = $property['default'] ?? null;
            $value = (empty($propertyValue) == true) ? $default : $propertyValue;
            
            $result[$key] = $value;         
        }    

        return $result;
    }

    /**
     * Clear property values
     *
     * @return void
     */
    public function clearValues(): void
    {
        $groups = $this->get('groups',[]);

        foreach ($this->data as $key => $value) {
            if (\in_array($key,$groups) === true) {
                foreach ($value as $name => $item) {
                    $this->data[$key][$name]['value'] = $this->data[$key][$name]['default'] ?? null;
                }
            } else {
                $this->data[$key]['value'] = $this->data[$key]['default'] ?? null;
            }    
        }
    }

    /**
     * Set value of every property from data array
     *
     * @param array $data
     * @return void
     */
    public function setPropertyValues(array $data): void
    {
        $groups = $this->get('groups',[]);

        foreach ($data as $key => $value) {
            if (\in_array($key,$groups) === true) {
                foreach ($value as $name => $item) {
                    if (isset($this->data[$key]) == true) {
                        $this->data[$key][$name]['value'] = $item;
                    }                   
                }
            } else {
                if (isset($this->data[$key]) == true) {
                    $this->data[$key]['value'] = $value;  
                };                     
            }    
        }
    }

    /**
     * Set property value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setPropertyValue(string $key, $value): bool
    {
        if (isset($this->data[$key]) == false) {
            return false;
        }
        $this->data[$key]['value'] = $value;
        
        return true;
    }
}
