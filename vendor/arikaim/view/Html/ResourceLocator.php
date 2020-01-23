<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Html;

use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Http\Url;

/**
 *  Resource locator
 */
class ResourceLocator   
{
    const UNKNOWN             = 0;
    const TEMPLATE_COMPONENT  = 1; 
    const EXTENSION_COMPONENT = 2;
    const GLOBAL_COMPONENT    = 3; 
    const RESOLVE_LOCATION    = 4;  
    const URL_RESOURCE        = 5;
    const FILE_RESOURCE       = 6;

    const EXTENSION_SELECTOR         = '::';
    const TEMPLATE_SELECTOR          = ':';
    const RESOLVE_LOCATION_SELECTOR  = '>';

    /**
     * Get resource selector type
     *
     * @param string $name
     * @return void
     */
    public static function getSelectorType($name)
    {
        if (stripos($name,'::') !== false) {
            return Self::EXTENSION_SELECTOR;
        }
        if (stripos($name,':') !== false) {
            return Self::TEMPLATE_SELECTOR;
        }
        if (stripos($name,'>') !== false) {
            return Self::RESOLVE_LOCATION_SELECTOR;
        }

        return null;
    }

    /**
     * Get resource type
     *
     * @param string $name
     * @return integer
     */
    public static function getType($name, $selectorType = null)
    {
        if (Utils::isValidUrl($name) == true) {
            return Self::URL_RESOURCE;
        }

        $selectorType = (empty($selectorType) == true) ? Self::getSelectorType($name) : $selectorType;
        $tokens = explode($selectorType,$name);  

        switch ($selectorType) {
            case Self::EXTENSION_SELECTOR:
                $type = Self::EXTENSION_COMPONENT;
                break;
            case Self::TEMPLATE_SELECTOR:             
                $type = ($tokens[0] == 'components') ? Self::GLOBAL_COMPONENT : Self::TEMPLATE_COMPONENT;   
                break;
            case Self::RESOLVE_LOCATION_SELECTOR:
                $type = Self::RESOLVE_LOCATION;
                break;
            default:
                $type = Self::UNKNOWN;           
        }

        return $type;
    }

    /**
     * Get resource url
     *
     * @param string $name
     * @return string $default
     */
    public static function getResourceUrl($name, $default = '')
    {
        $data = Self::parse($name);
    
        switch ($data['type']) {
            case Self::URL_RESOURCE:
                return $name;              
            case Self::TEMPLATE_COMPONENT:
                $templateUrl =  Url::getTemplateUrl($data['component_name']);                 
                return  $templateUrl . $data['path'];                          
        }

        return $default;
    }

    /**
     * Parse resource name 
     * 
     * @param string $name
     * @return array
     */
    public static function parse($name)
    {
        $selectorType = Self::getSelectorType($name);
        $tokens = explode($selectorType,$name);  
        $componentName = (isset($tokens[0]) == true) ? $tokens[0] : null;
        $path = (isset($tokens[1]) == true) ? $tokens[1] : null;
        $type = Self::getType($name);

        return [
            'path'           => $path,
            'component_name' => $componentName,
            'type' => $type
        ];
    }   
}
