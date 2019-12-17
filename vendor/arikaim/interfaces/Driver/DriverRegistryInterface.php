<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Driver;

/**
 * Driver Registry Interface
 */
interface DriverRegistryInterface
{    
    /**
      * Add driver
      *
      * @param string $name     Driver name        
      * @param array  $data     Driver data
      * @return boolean
    */
    public function addDriver($name, $data);

    /**
     * Remove driver
     *
     * @param string $name   
     * @return boolean
    */
    public function removeDriver($name);
    
    /**
     * Get driver
     *
     * @param string|integer $name Driver name
     * @return array|false
     */
    public function getDriver($name);

    /**
     * Return true if driver exist
     *
     * @param string $name  
     * @return boolean
     */
    public function hasDriver($name);

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array $config
     * @return boolean
     */
    public function saveConfig($name, $config);

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param integer $status    
     * @return boolean
     */
    public function setDriverStatus($name, $status);

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return array
     */
    public function getDriverConfig($name);

     /**
     * Get drivers list
     *
     * @param string|null $category
     * @param integer|null $status
     * @return array
     */
    public function getDriversList($category = null, $status = null);
}
