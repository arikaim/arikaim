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
 * Driver Manager Interface
 */
interface DriverManagerInterface
{    
    /**
     * Create driver
     *
     * @param string $name Driver name 
     * @param array $options  
     * @param array|null $config Drievr config properties
     * @return DriverInterface|null
     */
    public function create(string $name, array $options = [], ?array $config = null): ?object;

     /**
      * Install driver
      *
      * @param string|object $name Driver name
      * @param string|null $class full class name or driver object ref
      * @param string|null $category
      * @param string|null $title
      * @param string|null $description
      * @param string|null $version
      * @param array $config
      * @param string|null $packageName
      * @param string|null $packageType
      * @return boolean
    */
    public function install(
        $name, 
        ?string $class = null,
        ?string $category = null,
        ?string $title = null,
        ?string $description = null,
        ?string $version = null,
        array $config = [],
        ?string $packageName = null,
        ?string $packageType = null
    ): bool;

   /**
     * Uninstall driver
     *
     * @param string $name Driver name   
     * @return boolean
     */
    public function unInstall(string $name): bool;

    /**
     * Return true if driver exsits
     *
     * @param string $name Driver name
     * @return boolean
     */
    public function has(string $name): bool;

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array|object $config
     * @return boolean
     */
    public function saveConfig(string $name, $config): bool;

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return Properties
     */
    public function getConfig(string $name);

    /**
     * Enable driver
     *
     * @param string $name
     * @return boolean
     */
    public function enable(string $name): bool;

    /**
     * Disable driver
     *
     * @param string $name
     * @return boolean
     */
    public function disable(string $name): bool;

    /**
     * Get drivers list
     *
     * @param string|null   $category
     * @param integer|null  $status
     * @return array
     */
    public function getList(?string $category = null, ?int $status = null): array;
}
