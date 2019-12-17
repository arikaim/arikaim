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

use Arikaim\Core\Utils\File;
use Arikaim\Core\Collection\Properties;

/**
 * Properties factory class
 */
class PropertiesFactory
{ 
    /**
     * Create properties from array
     *
     * @param array $data
     * @return Properties
     */
    public static function createFromArray(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $property = (is_array($value) == true) ? Property::create($value) : new Property($key,$value);           
            $result[$key] = $property;
        }    

        return new Properties($result,false);   
    }

    /**
     * Create from file
     *
     * @param string $fileName
     * @param boolean $resolveProperties
     * @return Properties
     */
    public static function createFromFile($fileName, $resolveProperties = true)
    {
        $data = File::readJsonFile($fileName);
        $data = (is_array($data) == true) ? $data : [];

        return ($resolveProperties == true) ? new Properties($data) : Self::createFromArray($data);       
    }
}
