<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Framework\Router;

use Psr\Container\ContainerInterface;

use Arikaim\Core\Framework\Router\RouterInterface;
use Arikaim\Core\Routes\RouteType;
use Arikaim\Core\Access\Middleware\AuthMiddleware;
use Arikaim\Core\Framework\Router\Router;
use Exception;

/**
 * App router
 */
class ArikaimRouter extends Router implements RouterInterface
{
    /**
     * App container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Route loader
     *
     * @var null|object
     */
    protected $routeLoader;

    /**
     * Cache
     *
     * @var null|object
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @param object|null $routeLoader
     * @param CacheInterface|null $cache
     */
    public function __construct(ContainerInterface $container, $routeLoader = null, ?object $cache = null)
    {        
        parent::__construct();

        $this->cache = $cache ?? $container->get('cache');  
        $this->container = $container;
        $this->routeLoader = $routeLoader ?? $container->get('routes.storage');
    }

    /**
     * Dispatch route
     *
     * @param string $method
     * @param string $path
     * @param string|null $adminPagePath
     * @return array
     */
    public function dispatchRoute(string $method, string $path, ?string $adminPagePath = null): array
    {       
        $routeType = RouteType::getType($path,[
            'adminPagePath' => $adminPagePath ?? 'admin'
        ]);
    
        $cacheKey = $method . '.' . (string)$routeType;

        $variableRoutes = $this->cache->fetch('variable.routes.' . $cacheKey);
        $staticRoutes = $this->cache->fetch('static.routes.' . $cacheKey);
        
        $routeOptions = $this->cache->fetch('route.options.' . $cacheKey);
        if ($routeOptions !== false) {
            $this->routeOptions = $routeOptions;
        }
        
        $routeMiddlewares = $this->cache->fetch('route.middlewares.' . $cacheKey);
        if ($routeMiddlewares !== false) {
            $this->routeMiddlewares[$method] = $routeMiddlewares;
        }

        if ($variableRoutes === false || $staticRoutes === false) {   
            // map routes
            $this->loadRoutes($method,$routeType,$adminPagePath);
            list($staticRoutes,$variableRoutes) = $this->generator->getData($method);

            // save routes to cache
            $this->cache->save('variable.routes.' . $cacheKey,$variableRoutes); 
            $this->cache->save('static.routes.' . $cacheKey,$staticRoutes); 
            $this->cache->save('route.options.' . $cacheKey,$this->routeOptions);
            $this->cache->save('route.middlewares.' . $cacheKey,$this->getMiddlewares($method));
        }        

        // add admin twig extension                
        if ($routeType == RouteType::ADMIN_PAGE_URL ||
            RouteType::SYSTEM_API_URL ||
            RouteType::ADMIN_API_URL  ||
            RouteType::INSTALL_PAGE
        ) {
            $this->container->get('view')->addExtension(new \Arikaim\Core\App\AdminTwigExtension);
        }

        return $this->dispatch($method,$path,$staticRoutes,$variableRoutes);
    }

    /**
     * Load routes
     *
     * @param mixed $options  
     * @return int
     */
    public function loadRoutes(...$options): int
    {
        $method = $options[0];
        $type = $options[1];
        $adminPagePath = $options[2] ?? 'admin';

        switch($type) {
            case RouteType::HOME_PAGE_URL: 
                // home page route                 
                $this->mapRoutes($method,3);
                break;
            case RouteType::ADMIN_PAGE_URL: 
                // map control panel page
                $this->addRoute('GET','/' . $adminPagePath . '[/{language:[a-z]{2}}/]','Arikaim\Core\App\ControlPanel:loadControlPanel'); 
                $this->mapSystemRoutes($method);             
                break;
            case RouteType::SYSTEM_API_URL:              
                $this->mapSystemRoutes($method);      
                break;
            case RouteType::API_URL: 
                // api routes only 
                $this->mapRoutes($method,2);    
                break;
            case RouteType::ADMIN_API_URL:                
                // map admin api routes
                $this->mapRoutes($method,4);    
                $this->mapSystemRoutes($method);  
                break;
            case RouteType::INSTALL_PAGE: 
                $this->addRoute('GET','/admin/install','Arikaim\Core\App\InstallPage:loadInstall');
                break;
            case RouteType::UNKNOW_TYPE:                 
                $this->mapRoutes($method,$type);
                break;            
        }

        return $type;
    }

    /**
     * Get routes list for request method
     *
     * @param string $method
     * @param int|null $type
     * @return array
     */
    public function readRoutes(string $method, ?int $type = null): array
    {
        $cacheItemkey = 'routes.list.' . $method . '.' . ((string)$type ?? 'all');
        $routes = $this->cache->fetch($cacheItemkey);  
        if ($routes === false) {
            $routes = $this->routeLoader->searchRoutes($method,$type);
            $this->cache->save($cacheItemkey,$routes);   
        }
        
        return $routes;
    }

    /**
     * Map extensons and templates routes
     *     
     * @param string $method
     * @param int|null $type
     * @return boolean
     * 
     * @throws Exception
     */
    public function mapRoutes(string $method, ?int $type = null): bool
    {      
        try {   
            $routes = $this->readRoutes($method,$type);                           
        } catch(Exception $e) {
            return false;
        }
       
        foreach($routes as $item) {
            $handler = $item['handler_class'] . ':' . $item['handler_method'];
            $this->addRoute($method,$item['pattern'],$handler,[              
                'route_options'        => (empty($item['options'] ?? null) == true) ? [] : \json_decode($item['options'],true),
                'auth'                 => $item['auth'],
                'redirect'             => (empty($item['redirect_url']) == false) ? DOMAIN . BASE_PATH . $item['redirect_url'] : null,
                'route_page_name'      => $item['page_name'] ?? '',
                'route_extension_name' => $item['extension_name'] ?? ''
            ],$item['uuid']);

            // auth middleware
            if (empty($item['auth']) == false) {                              
                $this->addRouteMiddleware($method,$handler,AuthMiddleware::class);              
            } 
    
            $middlewares = $item['middleware'] ?? [];
            
            // add middlewares                        
            foreach ($middlewares as $class) {            
               $this->addRouteMiddleware($method,$handler,$class);                               
            }                                                                 
        }    
        
        return true;
    }

    /**
     * Map system routes
     *
     * @param string $method
     * @return void
     */
    protected function mapSystemRoutes(string $method): void
    {               
        $routes = \Arikaim\Core\App\SystemRoutes::$routes[$method] ?? [];
           
        foreach ($routes as $item) {          
            $this->addRoute($method,$item['pattern'],$item['handler'],[
                'route_options'        => [],
                'auth'                 => $item['auth'] ?? null,
                'redirect'             => null,
                'route_page_name'      => '',
                'route_extension_name' => ''
            ],\Arikaim\Core\Utils\Uuid::create());    
            
            if (empty($item['auth']) == false) {
                $this->addRouteMiddleware($method,$item['handler'],AuthMiddleware::class);                  
            }                                         
        }      
    } 
}
