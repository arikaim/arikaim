<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Driver\Traits;

/**
 * Driver trait
*/
trait Driver
{
    /**
     * Driver name
     *
     * @var string|null
    */
    protected $driverName = null;

    /**
     * Driver class
     *
     * @var string|null
     */
    protected $driverClass = null;

    /**
     * Driver version
     *
     * @var string
     */
    protected $driverVersion = '1.0.0';

    /**
     * Driver title (display name)
     *
     * @var string|null
     */
    protected $driverTitle = null;

    /**
     * Driver description
     *
     * @var string|null
     */
    protected $driverDescription = null;

    /**
     * Driver category
     *
     * @var string|null
     */
    protected $driverCategory = null;

    /**
     * Driver config
     *
     * @var array
     */
    protected $driverConfig = [];
    
    /**
     * Driver options
     *
     * @var array
     */
    protected $driverOptions = [];

    /**
     * Driver extension name
     *
     * @var string|null
     */
    protected $driverExtension = null;

    /**
     * Driver instance
     *
     * @var object|null
     */
    protected $instance;

    /**
     * Return driver name.
     *
     * @return string
     */
    public function getDriverName(): ?string
    {
        return $this->driverName;
    }

    /**
     * Get driver instance
     *
     * @return object
     */
    public function getInstance()
    {
        return $this->instance ?? $this;
    }

    /**
     * Return driver display name.
     *
     * @return string|null
     */
    public function getDriverTitle(): ?string
    {
        return $this->driverTitle ?? $this->getDriverName();
    }

    /**
     * Return driver description.
     *
     * @return string|null
     */
    public function getDriverDescription(): ?string
    {
        return $this->driverDescription;
    }

    /**
     * Return driver category.
     *
     * @return string|null
     */
    public function getDriverCategory(): ?string
    {
        return $this->driverCategory;
    }

    /**
     * Return driver version.
     *
     * @return string|null
     */
    public function getDriverVersion(): ?string
    {
        return $this->driverVersion;
    }

    /**
     * Return driver extension name (if driver class is located in extension)
     *
     * @return string|null
    */
    public function getDriverExtensionName(): ?string
    {
        return $this->driverExtension;
    }

    /**
     * Get driver class
     *
     * @return string
     */
    public function getDriverClass(): string
    {
        return $this->driverClass ?? \get_class();
    }

    /**
     * Set driver class
     *
     * @param string $class
     * @return void
     */
    public function setDriverClass(string $class): void
    {
        $this->driverClass = $class;
    }

    /**
     * Get driver config
     *
     * @return array
     */
    public function getDriverConfig(): array
    {
        return $this->driverConfig ?? [];
    }

    /**
     * Set driver config
     *
     * @param array $config
     * @return void
     */
    public function setDriverConfig(array $config): void
    {
        $this->driverConfig = $config;
    }

    /**
     * Get driver config var
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getDriverConfigVar(string $key, $default = null)
    {
        return $this->driverConfig[$key] ?? $default;
    }

    /**
     * Set driver name, title, category, description , version params
     *
     * @param string $name
     * @param string|null $category
     * @param string|null $title
     * @param string|null $class
     * @param string|null $description
     * @param string|null $version
     * @param string|null $extension
     * @return void
    */
    public function setDriverParams(
        string $name, 
        ?string $category = null,
        ?string $title = null,
        ?string $description = null,
        ?string $version = null,
        ?string $extension = null,
        ?string $class = null)
    {
        $this->driverName = $name;
        $this->driverCategory = $category;
        $this->driverTitle = $title;
        $this->driverClass = $class;
        $this->driverDescription = $description;
        $this->driverVersion = $version ?? '1.0.0';
        $this->driverExtension = $extension;
    }

    /**
     * Initialize driver
     *
     * @return void
     */
    public function initDriver($properties)
    {     
        $config = $properties->getValues();
        $this->instance = new $this->driverClass($config);   
    }

    /**
     * Build driver config properties
     *
     * @param Arikaim\Core\Collection\Properties $properties;
     * 
     * @return void
     */
    public function createDriverConfig($properties)
    {
    }

    /**
     * Get driver option
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getDriverOption(string $name, $default = null)
    {
        return $this->driverOptions[$name] ?? $default;
    }

    /**
     * Set driver option
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setDriverOption(string $name, $value): void
    {
        $this->driverOptions[$name] = $value;
    }

    /**
     * Set driver options
     *
     * @param array $options
     * @return void
     */
    public function setDriverOptions(array $options): void
    {
        $this->driverOptions = $options;
    } 

    /**
     * Get driver options
     *    
     * @return array
     */
    public function getDriverOptions(): array
    {
        return $this->driverOptions;
    } 
}
