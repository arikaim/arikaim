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

/**
 * Router interface
 */
interface RouterInterface
{
    public const ROUTE_NOT_FOUND = 0;
    public const ROUTE_FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

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
    public function addRoute(string $method, string $pattern, string $handlerClass, array $options = [], $routeId = null): void;

    /**
     * Get route middlewares
     *
     * @param string $method
     * @param string $handlerClass
     * @return array
     */
    public function getRouteMiddlewares(string $method, string $handlerClass): array;

    /**
     * Get reoute options
     *
     * @param string $method
     * @param string|int $id
     * @return array
     */
    public function getRouteOptions(string $method, $id): array;

    /**
     * Load routes
     *
     * @param mixed $options
  
     * @return int (route type)
     */
    public function loadRoutes(...$options): int;

    /**
     * Dispatch route
     *
     * @param string $method
     * @param string $uri
     * @param array $staticRoutes
     * @param array $variableRoutes
     * @return array
     */
    public function dispatch(string $method, string $uri, array $staticRoutes, array $variableRoutes): array;

    /**
     * Add route middleware
     *
     * @param string $method
     * @param string $handlerClass
     * @param string $middleware
     * @return void
     */
    public function addRouteMiddleware(string $method, string $handlerClass, string $middleware): void;
}
