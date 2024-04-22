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

use FastRoute\RouteParser\Std as RouteParser;

use Arikaim\Core\Framework\Router\RouterInterface;
use Arikaim\Core\Framework\Router\RouteGenerator;

/**
 * App router
 */
class Router implements RouterInterface
{
    /**
     * Route generator
     *
     * @var RouteGenerator
     */
    protected $generator;

    /**
     * Route loader
     *
     * @var null|object
     */
    protected $routeLoader;

    /**
     * Route middlewares
     *
     * @var array
     */
    protected $routeMiddlewares = [];

    /**
     * Route options
     *
     * @var array
     */
    protected $routeOptions = [];

    /**
     * Constructor
     *  
     */
    public function __construct()
    {        
        $this->generator = new RouteGenerator(new RouteParser());    
        $this->routeMiddlewares = [];
        $this->routeOptions = [];        
    }

    /**
     * Get route middlewares
     *
     * @param string $method
     * @param string $handlerClass
     * @return array
     */
    public function getRouteMiddlewares(string $method, string $handlerClass): array
    {
        return $this->routeMiddlewares[$method][$handlerClass] ?? [];
    }

    /**
     * Get middlewares per method
     *
     * @param string $method
     * @return array
     */
    public function getMiddlewares(string $method): array
    {
        return $this->routeMiddlewares[$method] ?? [];
    }

    /**
     * Add route middleware
     *
     * @param string $method
     * @param string $handlerClass
     * @param string $middleware
     * @return void
     */
    public function addRouteMiddleware(string $method, string $handlerClass, string $middleware): void
    {   
        $this->routeMiddlewares[$method][$handlerClass][] = $middleware;
    } 
    
    /**
     * Get route generator
     *
     * @return RouteGenerator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Dispatch route
     *
     * @param string $method
     * @param string $uri
     * @param array $staticRoutes
     * @param array $variableRoutes
     * @return array
     */
    public function dispatch(string $method, string $uri, array $staticRoutes, array $variableRoutes): array
    {       
        if (isset($staticRoutes[$method][$uri]) == true) {  
            $route = $staticRoutes[$method][$uri];        
        } elseif (isset($variableRoutes[$method]) == true) {
            $route = $this->dispatchVariableRoute($variableRoutes[$method],$uri);               
        }

        return [
            (($route ?? null) == null) ? RouterInterface::ROUTE_NOT_FOUND : RouterInterface::ROUTE_FOUND,
            $route ?? [
                'id'        => null,
                'methhod'   => $method,
                'handler'   => null,
                'regex'     => null,
                'variables' => []
            ]  
        ];  
    }

    /**
     * Add route
     *
     * @param string $method
     * @param string $pattern
     * @param string $handlerClass
     * @param array $options
     * @param string|int|null $routeId
     * @return void
     */
    public function addRoute(string $method, string $pattern, string $handlerClass, array $options = [], $routeId = null): void
    {      
        $this->generator->addRoute($method,$pattern,$handlerClass,$routeId);
               
        if (empty($routeId) == false) {
            $options['id'] = $routeId;
            $this->routeOptions[$method][$routeId] = $options;
        }
    }

    /**
     * Get reoute options
     *
     * @param string $method
     * @param string|int $id
     * @return array
     */
    public function getRouteOptions(string $method, $id): array
    {
        return (empty($id) == true) ? [] : $this->routeOptions[$method][$id] ?? [];
    }
    
    /**
     * Load routes
     *
     * @param mixed $options  
     * @return int
     */
    public function loadRoutes(...$options): int
    {       
        return 0;
    }

    /**
     * Dispatch variable route
     *
     * @param array $routes
     * @param string $uri
     * @return array|null
     */
    protected function dispatchVariableRoute(array $routes, string $uri): ?array
    {
        foreach ($routes as $data) {
            if (\preg_match($data['regex'],$uri,$matches) == false) {
                continue;
            }

            $route = $data['routeMap'][\count($matches)];
            $vars = [];
            $index = 0;

            foreach ($route['variables'] as $varName) {
                $vars[$varName] = $matches[++$index];
            }
            $route['variables'] = $vars;

            return $route;
        }

        return null;
    }
}
