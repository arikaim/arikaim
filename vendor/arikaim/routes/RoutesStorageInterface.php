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
 * Routes storage interface
 */
interface RoutesStorageInterface
{    
    /**
     * Add route middleware
     *
     * @param string $method
     * @param string $pattern
     * @param string $middlewareClass
     * @return bool
     */
    public function addMiddleware(string $method, string $pattern, string $middlewareClass): bool;

    /**
     * Get home page route
     *
     * @return array
     */
    public function getHomePageRoute(): array;
    
    /**
     * Get routes list for request method
     *
     * @param string $method
     * @param int|null $type
     * @return array
     */
    public function searchRoutes(string $method, ?int $type = null): array;
    
    /**
     * Save route redirect url
     *
     * @param string $method
     * @param string $pattern
     * @param string $url
     * @return boolean
     */
    public function saveRedirectUrl(string $method, string $pattern, string $url): bool;

    /**
     * Get extension routes
     *
     * @param array $filterfilter
     * @return array
     */
    public function getRoutes(array $filter = []): array;

    /**
     * Delete routes
     *
     * @param array $filter
     * @return boolean
     */
    public function deleteRoutes(array $filter = []): bool;

    /**
     * Set routes status
     *
     * @param array     $filter
     * @param integer   $status
     * @return boolean
     */
    public function setRoutesStatus(array $filter, int $status): bool;

    /**
     * Add route
     *
     * @param array $details
     * @return boolean
     */
    public function addRoute(array $details): bool;

    /**
     * Save route options
     *
     * @param string $method
     * @param string $pattern
     * @param array $options
     * @return boolean
     */
    public function saveRouteOptions(string $method, string $pattern, array $options): bool;

    /**
     * Return true if reoute exists
     *
     * @param string $method
     * @param string $pattern
     * @return boolean
     */
    public function hasRoute(string $method, string $pattern): bool;

    /**
     * Delete route
     *
     * @param string $method
     * @param string $pattern
     * @return bool
     */
    public function deleteRoute(string $method, string $pattern): bool;

    /**
     * Get route
     *
     * @param string $method
     * @param string $pattern
     * @return array|false
    */
    public function getRoute(string $method, string $pattern);

    /**
     * Get route details
     *
     * @param string|int $id  Route id or uuid
     * @return array|null
     */
    public function getRouteDetails($id): ?array;
}
