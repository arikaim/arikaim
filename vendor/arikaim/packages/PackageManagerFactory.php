<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages;

use Arikaim\Core\Interfaces\StorageInterface;
use Arikaim\Core\Interfaces\HttpClientInterface;
use Arikaim\Core\Interfaces\Packages\PackageManagerInterface;
use Arikaim\Core\Interfaces\Packages\PackageManagerFactoryInterface;
use Arikaim\Core\Interfaces\CacheInterface;
use Arikaim\Core\Packages\PackageManager;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Packages\PackageValidator;

/**
 * Package managers factory class
*/
class PackageManagerFactory implements PackageManagerFactoryInterface
{
    /**
     * Custom package classes
     *
     * @var array
     */
    private static $packageClass = [
        PackageManager::EXTENSION_PACKAGE          => 'Arikaim\\Core\\Packages\\ExtensionPackage',
        PackageManager::LIBRARY_PACKAGE            => 'Arikaim\\Core\\Packages\\LibraryPackage',
        PackageManager::TEMPLATE_PACKAGE           => 'Arikaim\\Core\\Packages\\TemplatePackage',
        PackageManager::MODULE_PACKAGE             => 'Arikaim\\Core\\Packages\\ModulePackage',
        PackageManager::COMPOSER_PACKAGE           => 'Arikaim\\Core\\Packages\\ComposerPackage',
        PackageManager::COMPONENTS_LIBRARY_PACKAGE => 'Arikaim\\Core\\Packages\\ComponentsLibraryPackage',
        PackageManager::SERVICE_PACKAGE            => 'Arikaim\\Core\\Packages\\ServicePackage'
    ];

    /**
     * Packages path
     *
     * @var array
     */
    private static $packagePath = [
        PackageManager::EXTENSION_PACKAGE           => Path::EXTENSIONS_PATH,
        PackageManager::LIBRARY_PACKAGE             => Path::LIBRARY_PATH,
        PackageManager::TEMPLATE_PACKAGE            => Path::TEMPLATES_PATH,
        PackageManager::MODULE_PACKAGE              => Path::MODULES_PATH,
        PackageManager::COMPOSER_PACKAGE            => Path::COMPOSER_VENDOR_PATH,
        PackageManager::COMPONENTS_LIBRARY_PACKAGE  => Path::COMPONENTS_PATH,
        PackageManager::SERVICE_PACKAGE             => Path::SERVICES_PATH
    ];

    /**
     * Package registry casses
     *
     * @var array
     */
    private static $packageRegistryClass = [
        PackageManager::EXTENSION_PACKAGE          => CORE_NAMESPACE . '\\Models\\Extensions',
        PackageManager::LIBRARY_PACKAGE            => null,
        PackageManager::TEMPLATE_PACKAGE           => null,
        PackageManager::MODULE_PACKAGE             => CORE_NAMESPACE . '\\Models\\Modules',
        PackageManager::COMPOSER_PACKAGE           => null,
        PackageManager::COMPONENTS_LIBRARY_PACKAGE => null,
        PackageManager::SERVICE_PACKAGE            => null
    ];

    /**
     * Package categories
     *
     * @var array
     */
    private static $packageCategory = [
        'themes'     => PackageManager::TEMPLATE_PACKAGE,
        'extensions' => PackageManager::EXTENSION_PACKAGE,
        'modules'    => PackageManager::MODULE_PACKAGE,   
        'composer'   => PackageManager::COMPOSER_PACKAGE,       
        'components' => PackageManager::COMPONENTS_LIBRARY_PACKAGE, 
        'services'   => PackageManager::SERVICE_PACKAGE,     
    ];

    /**
     * Cache
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * Local storage
     *
     * @var StorageInterface
     */
    private $storage;

    /**
     * Http client
     *
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * Constructor
     * 
     * @param CacheInterface $cache
     * @param StorageInterface $storage
     * @param HttpClientInterface $httpClient
     */
    public function __construct(CacheInterface $cache, StorageInterface $storage, HttpClientInterface $httpClient)
    {
        $this->cache = $cache;
        $this->storage = $storage;
        $this->httpClient = $httpClient;
    }

    /**
     * Create validator
     *
     * @param array|null $requires
     * @return PackageValidator
     */
    public function createValidator(?array $requires = [])
    {
        return new PackageValidator($requires);
    }

    /**
     * Create package manager
     *
     * @param string $packageType
     * @return PackageManagerInterface|null
     */
    public function create(string $packageType): ?object
    {
        if (\array_key_exists($packageType,Self::$packageCategory) === true) {
            $packageType = Self::$packageCategory[$packageType];
        }

        $packageClass = Self::getPackageClass($packageType);
        $path = Self::getPackagePath($packageType);
        $registry = Self::createPackageRegistry($packageType);

        return new PackageManager($path,$packageType,$packageClass,$this->cache,$this->storage,$this->httpClient,$registry);
    }

    /**
     * Create package registry
     *
     * @param string $packageType
     * @return object|null
     */
    public function registry(string $packageType): ?object
    {
        return Self::createPackageRegistry($packageType);
    }

    /**
     * Create package registry
     *
     * @param string $packageType
     * @return object|null
     */
    public static function createPackageRegistry(string $packageType): ?object
    {
        $class = Self::$packageRegistryClass[$packageType] ?? null;
        
        return (empty($class) == false) ? new $class() : null;
    }

    /**
     * Get package path
     *
     * @param string $packageType
     * @return string|null
     */
    public static function getPackagePath(string $packageType): ?string
    {
        return (isset(Self::$packagePath[$packageType]) == true) ? Self::$packagePath[$packageType] : null;
    }

    /**
     * Get package descriptor file name
     *
     * @param string $packageType
     * @param string $packageName
     * @return string
     */
    public static function getPackageDescriptorFileName(string $packageType, string $packageName): string
    {
        $path = Self::getPackagePath($packageType);

        return $path . $packageName . DIRECTORY_SEPARATOR . 'arikaim-package.json';
    }

    /**
     * Get package path
     *
     * @param string $packageType
     * @return string|null
     */
    public static function getPackageClass(string $packageType): ?string
    {
        return (isset(Self::$packageClass[$packageType]) == true) ? Self::$packageClass[$packageType] : null;
    }

    /**
     * Set package path
     *
     * @param string $packageType
     * @param string $path
     * @return void
     */
    public static function setPackagePath(string $packageType, string $path): void
    {
        Self::$packagePath[$packageType] = $path;
    }

    /**
     * Set package class
     *
     * @param string $packageType
     * @param string $class
     * @return void
     */
    public static function setPackageClass(string $packageType, string $class): void
    {
        Self::$packageClass[$packageType] = $class;
    }

    /**
     * Set package registry class
     *
     * @param string $packageType
     * @param string $class
     * @return void
     */
    public static function setPackageRegistryClass(string $packageType, string $class): void
    {
        Self::$packageRegistryClass[$packageType] = $class;
    }
}
