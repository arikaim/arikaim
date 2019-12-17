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
 * Driver interface
 */
interface DriverInterface
{    
    /**
     * Return driver name.
     *
     * @return string
     */
    public function getDriverName();

    /**
     * Return driver display name.
     *
     * @return string
     */
    public function getDriverTitle();

    /**
     * Return driver description.
     *
     * @return string
     */
    public function getDriverDescription();

    /**
     * Return driver category.
     *
     * @return string
     */
    public function getDriverCategory();

    /**
     * Return driver version.
     *
     * @return string
     */
    public function getDriverVersion();

    /**
     * Return driver extension name (if driver class is located in extension)
     *
     * @return string
    */
    public function getDriverExtensionName();

    /**
     * Get driver class
     *
     * @return string
    */
    public function getDriverClass();

    /**
     * Get driver config
     *
     * @return array
    */
    public function getDriverConfig();

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties);

    /**
     * Get driver instance
     *
     * @return object
     */
    public function getInstance();

    /**
     * Build driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return array
     */
    public function createDriverConfig($properties);
}
