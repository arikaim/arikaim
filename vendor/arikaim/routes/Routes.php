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
use Arikaim\Core\Routes\RouteType;
use Exception;

/**
 * Routes storage
*/
class Routes implements RoutesInterface
{
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
     * 
     * @param RoutesStorageInterface $adapter 
     * @param CacheInterface $cache
     */
    public function __construct(RoutesStorageInterface $adapter, CacheInterface $cache)
    {
        $this->adapter = $adapter;    
        $this->cache = $cache;
    }

    /**
     * Add route middleware
     *
     * @param string $method
     * @param string $pattern
     * @param string $middlewareClass
     * @return bool
     */
    public function addMiddleware(string $method, string $pattern, string $middlewareClass): bool
    {
        return $this->adapter->addMiddleware($method,$pattern,$middlewareClass);
    }
    
    /**
     * Set routes status
     *
     * @param array     $filterfilter
     * @param integer   $status
     * @return boolean
     */
    public function setRoutesStatus(array $filter, int $status): bool
    {
        return $this->adapter->setRoutesStatus($filter,$status);
    }

    /**
     * Save route redirect url
     *
     * @param string $method
     * @param string $pattern
     * @param string $url
     * @return boolean
     */
    public function setRedirectUrl(string $method, string $pattern, string $url): bool
    {
        return $this->adapter->saveRedirectUrl($method,$pattern,$url);
    }

    /**
     * Add template route
     *
     * @param string $pattern
     * @param string $handlerClass
     * @param string|null $handlerMethod
     * @param string $templateName
     * @param string|null $pageName
     * @param integer|null $auth
     * @param boolean $replace 
     * @param string|null $redirectUrl 
     * @param integer $type 
     * @param boolean $withLanguage
     * @return bool
     */
    public function saveTemplateRoute(
        string $pattern, 
        string $handlerClass, 
        ?string $handlerMethod, 
        string $templateName, 
        ?string $pageName, 
        $auth = null, 
        bool $replace = false, 
        ?string $redirectUrl = null,
        int $type = RoutesInterface::PAGE, 
        bool $withLanguage = true
    ): bool
    {
        $handlerMethod = ($handlerMethod == null) ? 'pageLoad' : $handlerMethod;
        $languagePattern = Route::getLanguagePattern($pattern);
        
        if ($replace == true) {           
            $this->delete('GET',$pattern);
            $this->delete('GET',$pattern . $languagePattern);
            if ($type == RoutesInterface::HOME_PAGE) {
                $this->deleteHomePage();
            }           
        }

        if ($this->has('GET',$pattern) == true) {
            return false;
        }

        if (Route::isValidPattern($pattern) == false) {           
            return false;
        }

        $pattern = ($withLanguage == true) ? $pattern . $languagePattern : $pattern;

        // check if exist with language pattern
        if ($this->has('GET',$pattern) == true) {
            return false;
        }
       
        $this->cache->delete('routes.list');

        if (Route::validate('GET',$pattern,$this->getAllRoutes()) == false) {
            return false;
        }
     
        return $this->adapter->addRoute([
            'method'         => 'GET',
            'pattern'        => $pattern,
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => $type,
            'page_name'      => $pageName,
            'template_name'  => $templateName,
            'redirect_url'   => $redirectUrl
        ]);
    }

    /**
     * Add home page route
     *
     * @param string $pattern
     * @param string $handlerClass
     * @param string $handlerMethod
     * @param string|null $extension
     * @param string $pageName
     * @param integer $auth  
     * @param string|null $name
     * @param boolean $withLanguage
     * @return bool
     */
    public function addHomePageRoute(
        string $pattern, 
        string $handlerClass, 
        string $handlerMethod, 
        ?string $extension, 
        $pageName, 
        $auth = null, 
        ?string $name = null, 
        bool $withLanguage = true
    )
    {
        return $this->addPageRoute(
            $pattern,
            $handlerClass,
            $handlerMethod,
            $extension,
            $pageName,
            $auth,
            $name,
            $withLanguage,
            RoutesInterface::HOME_PAGE
        );
    }

    /**
     * Add page route
     *
     * @param string $pattern
     * @param string $handlerClass
     * @param string $handlerMethod
     * @param string $extension
     * @param string|null $pageName
     * @param integer $auth  
     * @param string|null $name
     * @param boolean $withLanguage
     * @param integer $type
     * @return bool
     */
    public function addPageRoute(
        string $pattern, 
        string $handlerClass, 
        string $handlerMethod, 
        string $extension, 
        ?string $pageName, 
        ?string $auth = null, 
        ?string $name = null, 
        bool $withLanguage = true, 
        $type = RoutesInterface::PAGE
    )
    {
        if (Route::isValidPattern($pattern) == false) {           
            return false;
        }

        $languagePattern = Route::getLanguagePattern($pattern);
        // check if exist with language pattern
        if ($this->has('GET',$pattern . $languagePattern) == true) {
            return false;
        }
        if ($this->has('GET',$pattern) == true) {
            return false;
        }

        $this->cache->delete('routes.list');

        $pattern = ($withLanguage == true) ? $pattern . $languagePattern : $pattern;
        if (Route::validate('GET',$pattern,$this->getAllRoutes()) == false) {
            return false;
        }

        return $this->adapter->addRoute([
            'method'         => 'GET',
            'pattern'        => $pattern,
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => $type,
            'extension_name' => $extension,
            'page_name'      => $pageName,
            'name'           => $name,
            'regex'          => null
        ]);    
    }

    /**
     * Get language pattern
     *
     * @param string $pattern
     * @return string
     */
    public function getLanguagePattern(string $pattern): string
    {
        return Route::getLanguagePattern($pattern);
    }

    /**
     * Add api route
     *
     * @param string $method
     * @param string $pattern
     * @param string $handlerClass
     * @param string|null $handlerMethod
     * @param string|null $extension
     * @param integer|null $auth
     * @param int $type
     * @return bool
     * @throws Exception
     */
    public function addApiRoute(
        string $method,
        string $pattern, 
        string $handlerClass, 
        ?string $handlerMethod, 
        ?string $extension, 
        ?string $auth = null,
        int $type = RoutesInterface::API
    ): bool
    {
        if (Route::isValidPattern($pattern) == false) {           
            return false;
        }      
        if (RouteType::isValidApiRoutePattern($pattern) == false) {
            throw new Exception('Not valid api route pattern.',1);
            return false;
        }
 
        $this->cache->delete('routes.list');

        if (Route::validate($method,$pattern,$this->getAllRoutes()) == false) {
            return false;
        }

        return $this->adapter->addRoute([
            'method'         => $method,
            'pattern'        => $pattern,
            'handler_class'  => $handlerClass,
            'handler_method' => $handlerMethod,
            'auth'           => $auth,
            'type'           => $type,
            'regex'          => null,
            'extension_name' => $extension
        ]);    
    }

    /**
     * Return true if reoute exists
     *
     * @param string $method
     * @param string $pattern
     * @return boolean
     */
    public function has(string $method, string $pattern): bool
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
    public function delete(string $method, string $pattern): bool
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
    public function saveRouteOptions(string $method, string $pattern, $options): bool
    {
        return $this->adapter->saveRouteOptions($method,$pattern,$options);
    }

    /**
     * Delete home page route
     *
     * @return boolean
     */
    public function deleteHomePage(): bool
    {
        return $this->adapter->deleteRoutes(['type' => RoutesInterface::HOME_PAGE]);
    }

    /**
     * Delete routes
     *
     * @param array $filterfilter
     * @return boolean
     */
    public function deleteRoutes($filter = []): bool
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
    public function getRoute(string $method, string $pattern)
    {
        return $this->adapter->getRoute($method,$pattern);
    }

    /**
     * Get route details
     *
     * @param string|int $id  Route id or uuid
     * @return array|null
     */
    public function getRouteDetails($id): ?array
    {
        return $this->adapter->getRouteDetails($id);
    }

    /**
     * Get routes
     *
     * @param array $filter  
     * @return array
     */
    public function getRoutes(array $filter = [])
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
        if ($routes === false) {
            $routes = $this->getRoutes(['status' => 1]);  
            $this->cache->save('routes.list',$routes);         
        }

        return $routes;
    }

    /**
     * Get routes list for request method
     *
     * @param string $method
     * @param int|null $type
     * @return array
     */
    public function searchRoutes(string $method, $type = null): array
    {
        $cacheItemkey = 'routes.list.' . $method . '.' . ($type ?? 'all');
        $routes = $this->cache->fetch($cacheItemkey);  
        if ($routes === false) {
            $routes = $this->adapter->searchRoutes($method,$type);
            $this->cache->save($cacheItemkey,$routes);   
        }
        
        return $routes;
    }

    /**
     * Get home page route
     *
     * @return array
     */
    public function getHomePageRoute(): array
    {
        return $this->adapter->getHomePageRoute();
    }
}
