<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Template;

/**
 * Tmplate tests functions
 */
class Tests  
{
   /**
     * Return true if var is object
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isObject($var)
    {
        return is_object($var);
    }

    /**
     * Return true if var is string
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isString($var)
    {
        return is_string($var);
    }

    /**
     * Compare version (if version1 is > version2 retrun true)
     *
     * @param string $version1
     * @param string $version2
     * @return boolean
     */
    public static function versionCompare($version1, $version2)
    {
        return version_compare($version1,$version2,'>'); 
    }
}
