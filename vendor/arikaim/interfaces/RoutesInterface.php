<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Routes interface
 */
interface RoutesInterface
{    
    /**
     *  Route type constant
     */
    const PAGE      = 1;
    const API       = 2;
    const HOME_PAGE = 3;
    const ADMIN_API = 4;

    /**
     * Get home page route
     *
     * @return array
    */
    public function getHomePageRoute();

    /**
     * Get route
     *
     * @param string $method
     * @param string $pattern
     * @return array|false
    */
    public function getRoute(string $method, string $pattern);

     /**
     * Get routes list for request method
     *
     * @param string $method
     * @return array
     */
    public function searchRoutes(string $method, $type = null);
    
    /**
     * Save route redirect url
     *
     * @param string $method
     * @param string $pattern
     * @param string $url
     * @return boolean
     */
    public function setRedirectUrl(string $method, string $pattern, string $url): bool;

    /**
     * Set routes status
     *
     * @param array     $filterfilter
     * @param integer   $status
     * @return boolean
     */
    public function setRoutesStatus(array $filter, int $status): bool;

    /**
     * Add api route
     *
     * @param string $method
     * @param string $pattern
     * @param string $handlerClass
     * @param string|null $handlerMethod
     * @param string|null $extension
     * @param integer|string|null $auth
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
    ): bool;

    /**
     * Return true if reoute exists
     *
     * @param string $method
     * @param string $pattern
     * @return boolean
     */
    public function has(string $method, string $pattern): bool;

    /**
     * Delete route
     *
     * @param string $method
     * @param string $pattern
     * @return bool
     */
    public function delete(string $method, string $pattern): bool;

    /**
     * Save route options
     *
     * @param string $method
     * @param string $pattern
     * @param array $options
     * @return boolean
     */
    public function saveRouteOptions(string $method, string $pattern, $options): bool;
}
