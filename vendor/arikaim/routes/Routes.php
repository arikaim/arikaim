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

use FastRoute\RouteParser\Std;

use Arikaim\Core\Routes\RoutesStorageInterface;
use Arikaim\Core\Interfaces\RoutesInterface;
use Arikaim\Core\Interfaces\CacheInterface;
use Exception;

/**
 * Routes storage
*/
class Routes implements RoutesInterface
{
    /**
     *  Route type constant
     */
    const PAGE   = 1;
    const API    = 2;

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
     * @return bool
     */
    public function saveTemplateRoute($pattern, $handlerClass, $handlerMethod, $templateName, $pageName, $auth = null)
    {
        $handlerMethod = ($handlerMethod == null) ? "loadPage" : $handlerMethod;
        $route = [
            'method'         => "GET",
            'pattern'        => $pattern . $this->getLanguagePattern($pattern),
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => Self::PAGE,
            'page_name'      => $pageName,
            'template_name'  => $templateName
        ];
        
        return $this->adapter->addRoute($route);
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
     * @return bool
     */
    public function addPageRoute($pattern, $handlerClass, $handlerMethod, $extension, $pageName, $auth = null, $name = null, $withLanguage = true)
    {
        $languagePattern = ($withLanguage == true) ? $this->getLanguagePattern($pattern) : '';
        $route = [
            'method'            => "GET",
            'pattern'           => $pattern . $languagePattern,
            'handler_class'     => $handlerClass,
            'handler_method'    => $handlerMethod,
            'auth'              => $auth,
            'type'              => Self::PAGE,
            'extension_name'    => $extension,
            'page_name'         => $pageName,
            'name'              => $name,
        ];

        return $this->adapter->addRoute($route);    
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
        $route = [
            'method'         => $method,
            'pattern'        => $pattern,
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => Self::API,
            'extension_name' => $extension
        ];

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
     * Return true if route pattern have placeholder
     *
     * @return boolean
     */
    public function hasPlaceholder($pattern)
    {
        return preg_match("/\{(.*?)\}/",$pattern);
    }

    /**
     * Get language route path  
     *
     * @param string $path
     * @return string
     */
    public function getLanguagePattern($path)
    {        
        return (substr($path,-1) == "/") ? "[{language:[a-z]{2}}/]" : "[/{language:[a-z]{2}}/]";
    }

    /**
     * Get route url
     *
     * @param string $pattern
     * @param array  $data
     * @param array  $queryParams
     * @return string
     */
    public function getRouteUrl($pattern, array $data = [], array $queryParams = [])
    {      
        if ($this->hasPlaceholder($pattern) == false) {           
            return $pattern;
        }

        $segments = [];      
        $parser = new Std();
        
        try {
            $expressions = array_reverse($parser->parse($pattern));
        } catch (Exception $e) {
        }
       
        foreach ($expressions as $expression) {

            foreach ($expression as $segment) {               
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }
                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }
                $segments[] = $data[$segment[0]];
            }            
            
            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments) == true) {
            return $pattern;
        }

        $url = implode('',$segments);
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
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

    /**
     * Return true if route pattern is valid
     *
     * @param string $pattern
     * @return boolean
     */
    public function isValidPattern($pattern)
    {
        $parser = new Std();
        try {
            $parser->parse($pattern);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    
}
