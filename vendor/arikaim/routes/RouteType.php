<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Routes;

/**
 * Route types
 */
class RouteType 
{
    const INSTALL_PAGE_URL_PATH = 'admin/install';
    
    const UNKNOW_TYPE    = 0;
    const HOME_PAGE_URL  = 1;
    const ADMIN_PAGE_URL = 2;
    const SYSTEM_API_URL = 3;
    const API_URL        = 4;
    const ADMIN_API_URL  = 5;
    const INSTALL_PAGE   = 6;

    /**
     * Get route type
     * 
     * @param string $path
     * @param array $options
     * @return int
     */
    public static function getType(string $url, array $options = []): int 
    {
        $url = \rtrim(\str_replace(BASE_PATH,'',$url),'/');

        if (\substr($url,-13) == Self::INSTALL_PAGE_URL_PATH) {
            return Self::INSTALL_PAGE;
        }


        $segments = \explode('/',$url);
        $count = \count($segments);
        
        // check for system api 
        $segments[1] = $segments[1] ?? '';
        $segments[2] = $segments[2] ?? '';
        $segments[3] = $segments[3] ?? '';
        $adminPagePath = $options['adminPagePath'] ?? 'admin';

        if ($segments[1] == 'core' && $segments[2] == 'api') {            
            return Self::SYSTEM_API_URL;
        }

        // check for api 
        if ($segments[1] == 'api') {
            return ($segments[3] == 'admin' || $segments[2] == 'admin') ? Self::ADMIN_API_URL : Self::API_URL;
        }

        // check for admin 
        if (($segments[1] == $adminPagePath) && ($count <= 3)) {
            return Self::ADMIN_PAGE_URL;
        }

        // check for home page
        if ($count == 1 || $count == 2) {
            if (empty($segments[1]) == true && empty($segments[2])== true) {              
                return Self::HOME_PAGE_URL;
            }
            if (Self::isLanguageSegment([$segments[0],$segments[1]]) == true) {               
                return Self::HOME_PAGE_URL;
            }
        }
       
        return Self::UNKNOW_TYPE;
    }

    /**
     * Return true if last url segment is language 
     *
     * @param array $urlSegments
     * @return boolean
     */
    public static function isLanguageSegment(array $urlSegments): bool
    {
        return (\strlen(\last($urlSegments)) == 2);
    }

    /**
     * Return true if request url is system api
     *
     * @param string $url
     * @return boolean
     */
    public static function isSystemApiUrl(string $url): bool
    {
        return (\substr(\str_replace(BASE_PATH,'',$url),0,10) == '/core/api/');
    }

    /**
     * Return true if request is for installation 
     *
     * @param string|null $uri
     * @return boolean
     */
    public static function isApiInstallRequest(?string $uri = null): bool
    {
        $uri = $uri ?? $_SERVER['REQUEST_URI'] ?? '';
    
        return (\substr(\str_replace(BASE_PATH,'',$uri),0,18) == '/core/api/install/');
    }

    /**
     * Check for install page url
     * 
     * @param string|null $uri
     * @return boolean
     */
    public static function isInstallPage(?string $uri = null): bool
    {
        $uri = $uri ?? $_SERVER['REQUEST_URI'] ?? '';
       
        return (\substr($uri,-13) == Self::INSTALL_PAGE_URL_PATH);
    }

    /**
     * Get install page url
     *
     * @return string
     */
    public static function getInstallPageUrl() : string
    {
        return DOMAIN . BASE_PATH . '/' . Self::INSTALL_PAGE_URL_PATH;
    }

    /**
     * Return true if api url pattern is valid
     *
     * @param string $pattern
     * @return boolean
     */
    public static function isValidApiRoutePattern(string $pattern): bool
    {
        return (\substr($pattern,0,4) == '/api');
    }
}
