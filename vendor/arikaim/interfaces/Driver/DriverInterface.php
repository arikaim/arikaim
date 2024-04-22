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
     * @return string|null
     */
    public function getDriverName(): ?string;

    /**
     * Return driver display name.
     *
     * @return string|null
     */
    public function getDriverTitle(): ?string;

    /**
     * Return driver description.
     *
     * @return string|null
     */
    public function getDriverDescription(): ?string;

    /**
     * Return driver category.
     *
     * @return string|null
     */
    public function getDriverCategory(): ?string;

    /**
     * Return driver version.
     *
     * @return string|null
     */
    public function getDriverVersion(): ?string;

    /**
     * Return driver extension name (if driver class is located in extension)
     *
     * @return string|null
    */
    public function getDriverExtensionName(): ?string;

    /**
     * Get driver class
     *
     * @return string
    */
    public function getDriverClass(): string;

    /**
     * Get driver config
     *
     * @return array
    */
    public function getDriverConfig(): array;

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
     * @return void
     */
    public function createDriverConfig($properties);

    /**
     * Set driver options
     *
     * @param array $options
     * @return void
     */
    public function setDriverOptions(array $options);

    /**
     * Set driver config
     *
     * @param array $config
     * @return void
     */
    public function setDriverConfig(array $config): void;
}
