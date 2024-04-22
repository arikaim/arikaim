<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
*/
namespace Arikaim\Core\View\Template;

use Arikaim\Core\Utils\Utils;

/**
 * Template tests functions
 */
class Tests  
{
   /**
     * Return true if var is object
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isObject($var): bool
    {
        return \is_object($var);
    }

    /**
     * Return true if var is string
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isString($var): bool
    {
        return \is_string($var);
    }

    /**
     * Compare version (if requiredVersion is > currentVersion retrun true)
     *
     * @param string|null $requiredVersion
     * @param string|null $currentVersion   
     * @return boolean
     */
    public static function versionCompare(?string $requiredVersion, ?string $currentVersion): bool
    {
        return Utils::checkVersion($currentVersion,$requiredVersion,'>');  
    }
}
