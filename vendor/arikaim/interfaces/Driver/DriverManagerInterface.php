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
     * @return DriverInterface|false
     */
    public function create($name);

    /**
      * Install driver
      *
      * @param string|object $name Driver name
      * @param string $class full class name or driver object ref
      * @param string|null $category
      * @param string|null $title
      * @param string|null $description
      * @param string|null $version
      * @param array $config
      * @param string|null $extension
      * @return boolean
    */
    public function install($name, $class, $category = null, $title = null, $description = null, $version = null, $config = [], $extension = null);

    /**
     * Uninstall driver
     *
     * @param string $name Driver name   
     * @return boolean
     */
    public function unInstall($name);

    /**
     * Return true if driver exsits
     *
     * @param string $name Driver name
     * @return boolean
     */
    public function has($name);

    /**
     * Save driver config
     *
     * @param string $name Driver name
     * @param array|object $config
     * @return boolean
     */
    public function saveConfig($name, $config);

    /**
     * Get driver config
     *
     * @param string $name Driver name
     * @return Properties
     */
    public function getConfig($name);

    /**
     * Enable driver
     *
     * @param string $name
     * @return boolean
     */
    public function enable($name);

    /**
     * Disable driver
     *
     * @param string $name
     * @return boolean
     */
    public function disable($name);

    /**
     * Get drivers list
     *
     * @param string|null   $category
     * @param integer|null  $status
     * @return array
     */
    public function getList($category = null, $status = null);
}
