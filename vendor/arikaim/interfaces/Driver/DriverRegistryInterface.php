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
    public function addDriver(string $name, array $data): bool;

    /**
     * Remove driver
     *
     * @param string $name   
     * @return boolean
    */
    public function removeDriver(string $name): bool;
    
    /**
     * Get driver
     *
     * @param string|integer $name Driver name
     * @return array|false
     */
    public function getDriver(string $name);

    /**
     * Return true if driver exist
     *
     * @param string $name  
     * @return boolean
     */
    public function hasDriver(string $name): bool;

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array $config
     * @return boolean
     */
    public function saveConfig(string $name, array $config): bool;

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param integer|string $status    
     * @return boolean
     */
    public function setDriverStatus(string $name, $status): bool;

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return array
     */
    public function getDriverConfig(string $name);

     /**
     * Get drivers list
     *
     * @param string|null $category
     * @param integer|null $status
     * @return array
     */
    public function getDriversList(?string $category = null, ?int $status = null): array;
}
