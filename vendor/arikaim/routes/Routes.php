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

use Arikaim\Core\Routes\RoutesStorageInterface;
use Arikaim\Core\Interfaces\RoutesInterface;
use Arikaim\Core\Interfaces\CacheInterface;
use Arikaim\Core\Routes\Route;

/**
 * Routes storage
*/
class Routes implements RoutesInterface
{
    /**
     *  Route type constant
     */
    const PAGE      = 1;
    const API       = 2;
    const HOME_PAGE = 3;

    /**
     * Routes storage adapter
     *
     * @var RoutesStorageInterface
     */
    protected $adapter;

    /**
     * Cache
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Constructor
     */

    public function __construct(RoutesStorageInterface $adapter, CacheInterface $cache)
    {
        $this->adapter = $adapter;    
        $this->cache = $cache;
    }

    /**
     * Set routes status
     *
     * @param array     $filterfilter
     * @param integer   $status
     * @return boolean
     */
    public function setRoutesStatus($filter = [], $status)
    {
        return $this->adapter->setRoutesStatus($filter,$status);
    }

    /**
     * Add template route
     *
     * @param string $pattern
     * @param string $handlerClass
     * @param string $handlerMethod
     * @param string $templateName
     * @param string $pageName
     * @param integer $auth
     * @param boolean $replace 
     * @param string|null $redirectUrl 
     * @param integer $type
     * @return bool
     */
    public function saveTemplateRoute($pattern, $handlerClass, $handlerMethod, $templateName, $pageName, $auth = null, $replace = false, $redirectUrl = null, $type = Self::PAGE)
    {
        $handlerMethod = ($handlerMethod == null) ? "pageLoad" : $handlerMethod;

        $route = [
            'method'         => "GET",
            'pattern'        => $pattern,
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => $type,
            'page_name'      => $pageName,
            'template_name'  => $templateName,
            'redirect_url'   => $redirectUrl
        ];
        
        if ($replace == true) {
            $this->delete('GET',$pattern);
            if ($type == Self::HOME_PAGE) {
                $this->deleteHomePage();
            }
        }

        $this->cache->delete('routes.list');

        if (Route::validate("GET",$pattern,$this->getAllRoutes()) == false) {
            return false;
        }
     
        return $this->adapter->addRoute($route);
    }

    /**
     * Add home page route
     *
     * @param string $pattern
     * @param string $handlerClass
     * @param string $handlerMethod
     * @param string $extension
     * @param string $pageName
     * @param integer $auth  
     * @param string|null $name
     * @param boolean $withLanguage
     * @return bool
     */
    public function addHomePageRoute($pattern, $handlerClass, $handlerMethod, $extension, $pageName, $auth = null, $name = null, $withLanguage = true)
    {
        return $this->addPageRoute($pattern,$handlerClass,$handlerMethod,$extension,$pageName,$auth,$name,$withLanguage,Self::HOME_PAGE);
    }

    /**
     * Add page route
     *
     * @param string $pattern
     * @param string $handlerClass
     * @param string $handlerMethod
     * @param string $extension
     * @param string $pageName
     * @param integer $auth  
     * @param string|null $name
     * @param boolean $withLanguage
     * @param integer $type
     * @return bool
     */
    public function addPageRoute($pattern, $handlerClass, $handlerMethod, $extension, $pageName, $auth = null, $name = null, $withLanguage = true, $type = Self::PAGE)
    {
        if (Route::isValidPattern($pattern) == false) {           
            return false;
        }

        $languagePattern = Route::getLanguagePattern($pattern);
        if ($this->has('GET',$pattern . $languagePattern) == true) {
            return false;
        }
        $pattern = ($withLanguage == true) ? $pattern . $languagePattern : $pattern;

        $route = [
            'method'            => "GET",
            'pattern'           => $pattern,
            'handler_class'     => $handlerClass,
            'handler_method'    => $handlerMethod,
            'auth'              => $auth,
            'type'              => $type,
            'extension_name'    => $extension,
            'page_name'         => $pageName,
            'name'              => $name,
        ];

        $this->cache->delete('routes.list');
        
        if (Route::validate("GET",$pattern,$this->getAllRoutes()) == false) {
            return false;
        }

        return $this->adapter->addRoute($route);    
    }

    /**
     * Get language pattern
     *
     * @param string $pattern
     * @return string
     */
    public function getLanguagePattern($pattern)
    {
        return Route::getLanguagePattern($pattern);
    }

    /**
     * Add api route
     *
     * @param string $method
     * @param string $pattern
     * @param string $handlerClass
     * @param string $handlerMethod
     * @param string $extension
     * @param integer|null $auth
     * @return bool
     */
    public function addApiRoute($method, $pattern, $handlerClass, $handlerMethod, $extension, $auth = null)
    {
        if (Route::isValidPattern($pattern) == false) {           
            return false;
        }

        $route = [
            'method'         => $method,
            'pattern'        => $pattern,
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => Self::API,
            'extension_name' => $extension
        ];
        
        $this->cache->delete('routes.list');

        if (Route::validate($method,$pattern,$this->getAllRoutes()) == false) {
            return false;
        }

        return $this->adapter->addRoute($route);    
    }

    /**
     * Return true if reoute exists
     *
     * @param string $method
     * @param string $pattern
     * @return boolean
     */
    public function has($method, $pattern)
    {
        return $this->adapter->hasRoute($method,$pattern);
    }

    /**
     * Delete route
     *
     * @param string $method
     * @param string $pattern
     * @return bool
     */
    public function delete($method, $pattern)
    {
        return $this->adapter->deleteRoute($method,$pattern);
    }

    /**
     * Save route options
     *
     * @param string $method
     * @param string $pattern
     * @param array $options
     * @return boolean
     */
    public function saveRouteOptions($method, $pattern, $options)
    {
        return $this->adapter->saveRouteOptions($method,$pattern,$options);
    }

    /**
     * Delete home page route
     *
     * @return boolean
     */
    public function deleteHomePage()
    {
        return $this->adapter->deleteRoutes(['type' => Self::HOME_PAGE]);
    }

    /**
     * Delete routes
     *
     * @param array $filterfilter
     * @return boolean
     */
    public function deleteRoutes($filter = [])
    {
        return $this->adapter->deleteRoutes($filter);
    }

    /**
     * Get route
     *
     * @param string $method
     * @param string $pattern
     * @return array|false
    */
    public function getRoute($method, $pattern)
    {
        return $this->adapter->getRoute($method,$pattern);
    }

    /**
     * Get routes
     *
     * @param array $filter  
     * @return array
     */
    public function getRoutes($filter = [])
    {
        return $this->adapter->getRoutes($filter);
    }

    /**
     * Get all actve routes from storage
     *
     * @return array
     */
    public function getAllRoutes()
    {
        $routes = $this->cache->fetch('routes.list');
        if (is_array($routes) == false) {
            $routes = $this->getRoutes(['status' => 1]);  
            $this->cache->save('routes.list',$routes,4);         
        }

        return $routes;
    }
}
