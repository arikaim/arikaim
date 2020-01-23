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
     * Get extension routes
     *
     * @param array $filterfilter
     * @return array
     */
    public function getRoutes($filter = []);

    /**
     * Delete routes
     *
     * @param array $filterfilter
     * @return boolean
     */
    public function deleteRoutes($filter = []);

    /**
     * Set routes status
     *
     * @param array     $filterfilter
     * @param integer   $status
     * @return boolean
     */
    public function setRoutesStatus($filter = [], $status);

    /**
     * Add route
     *
     * @param array $details
     * @return boolean
     */
    public function addRoute(array $details);

    /**
     * Save route options
     *
     * @param string $method
     * @param string $pattern
     * @param array $options
     * @return boolean
     */
    public function saveRouteOptions($method, $pattern, array $options);

    /**
     * Return true if reoute exists
     *
     * @param string $method
     * @param string $pattern
     * @return boolean
     */
    public function hasRoute($method, $pattern);

    /**
     * Delete route
     *
     * @param string $method
     * @param string $pattern
     * @return bool
     */
    public function deleteRoute($method, $pattern);

    /**
     * Get route
     *
     * @param string $method
     * @param string $pattern
     * @return array|false
    */
    public function getRoute($method, $pattern);
}
