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
        return new Properties($data);   
    }

    /**
     * Create from file
     *
     * @param string $fileName
     * @param boolean $resolveProperties
     * @return Properties
     */
    public static function createFromFile(string $fileName, bool $resolveProperties = true)
    {
        $data = File::readJsonFile($fileName);
        $data = ($data === false) ? [] : $data;

        return ($resolveProperties == true) ? new Properties($data) : Self::createFromArray($data);       
    }
}
