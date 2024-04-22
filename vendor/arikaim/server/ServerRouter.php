<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Server;

use Psr\Container\ContainerInterface;

use Arikaim\Core\Framework\Router\RouterInterface;
use Arikaim\Core\Routes\RouteType;
use Arikaim\Core\Http\Url;
use Arikaim\Core\Interfaces\RoutesInterface;
use Arikaim\Core\App\SystemRoutes;
use Arikaim\Core\Access\Middleware\AuthMiddleware;
use Arikaim\Core\Framework\Router\Router;
use Arikaim\Core\Utils\Uuid;
use Exception;

/**
 * Server router
 */
class ServerRouter extends Router implements RouterInterface
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
     * Constructor
     *
     * @param ContainerInterface $container
     * @param string $basePath
     * @param object|null $routeLoader
     */
    public function __construct(ContainerInterface $container, string $basePath, $routeLoader = null)
    {        
        parent::__construct($basePath);
       
        $this->container = $container;
        $this->routeLoader = ($routeLoader == null) ? $container->get('routes') : $routeLoader;
    }

    /**
     * Load routes
     *
     * @param mixed $options  
     * @return void
     */
    public function loadRoutes(...$options): void
    {
        $type = $options[0];

        switch($type) {
            case RouteType::HOME_PAGE_URL: 
                // home page route                 
                $this->mapRoutes(RoutesInterface::HOME_PAGE);
                break;
            case RouteType::ADMIN_PAGE_URL: 
                // add admin twig extension                
                $this->container->get('view')->addExtension(new \Arikaim\Core\App\AdminTwigExtension);
                // map control panel page
                $this->addRoute('GET','/admin[/{language:[a-z]{2}}/]','Arikaim\Core\App\ControlPanel:loadControlPanel');
                // map install page
                $this->addRoute('GET','/admin/install','Arikaim\Core\App\InstallPage:loadInstall');
                break;
            case RouteType::SYSTEM_API_URL: 
                // add admin twig extension
                $this->container->get('view')->addExtension(new \Arikaim\Core\App\AdminTwigExtension);                 
                $this->mapSystemRoutes();       
                break;
            case RouteType::API_URL: 
                // api routes only 
                $this->mapRoutes(RoutesInterface::API);    
                break;
            case RouteType::ADMIN_API_URL:                
                // add admin twig extension
                $this->container->get('view')->addExtension(new \Arikaim\Core\App\AdminTwigExtension);
                // map admin api routes
                $this->mapRoutes(RoutesInterface::API);    
                $this->mapRoutes(RoutesInterface::ADMIN_API);    
                break;
            case RouteType::UNKNOW_TYPE:                 
                $this->mapRoutes(RoutesInterface::PAGE);
                break;            
        }
    }

    /**
     * Map extensons and templates routes
     *       
     * @param int|null $type
     * @return boolean
     * 
     * @throws Exception
     */
    public function mapRoutes(?int $type = null): bool
    {      
        try {   
            if ($type == RoutesInterface::HOME_PAGE) {
                $routes = $this->routeLoader->getHomePageRoute();
            } else {
                $routes = $this->routeLoader->getRoutes([
                    'status' => 1,
                    'type'   => $type                   
                ]);   
            }
                                   
        } catch(Exception $e) {
            return false;
        }
       
        foreach($routes as $item) {
            $handler = $item['handler_class'] . ':' . $item['handler_method'];
            $this->addRoute($item['method'],$item['pattern'],$handler,[
                'route_options'        => $item['options'] ?? null,
                'auth'                 => $item['auth'],
                'redirect_url'         => (empty($item['redirect_url']) == false) ? Url::BASE_URL . $item['redirect_url'] : null,
                'route_page_name'      => $item['page_name'] ?? '',
                'route_extension_name' => $item['extension_name'] ?? ''
            ],$item['uuid']);

            // auth middleware
            if (empty($item['auth']) == false) {                              
                $this->addRouteMiddleware($item['method'],$handler,AuthMiddleware::class);              
            } 
    
            $middlewares = (\is_string($item['middlewares']) == true) ? \json_decode($item['middlewares'],true) : $item['middlewares'] ?? [];
            // add middlewares                        
            foreach ($middlewares as $class) {            
               $this->addRouteMiddleware($item['method'],$handler,$class);                               
            }                                                                 
        }    
        
        return true;
    }

    /**
     * Map system routes
     *  
     * @return void
     */
    protected function mapSystemRoutes(): void
    {             
        $systemMoutes = SystemRoutes::$installRoutes;

        foreach ($systemMoutes as $method => $routes) {     

            foreach ($routes as $item) {          
                $this->addRoute($method,$item['pattern'],$item['handler'],[
                    'route_options'        => null,
                    'auth'                 => $item['auth'] ?? null,
                    'redirect_url'         => null,
                    'route_page_name'      => '',
                    'route_extension_name' => ''
                ],Uuid::create());    
                
                if (empty($item['auth']) == false) {
                    $this->addRouteMiddleware($method,$item['handler'],AuthMiddleware::class);                  
                }                                         
            }  

        }
    } 
}
