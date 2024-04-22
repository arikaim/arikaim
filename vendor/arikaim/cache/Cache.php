<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Cache
*/
namespace Arikaim\Core\Cache;

use Arikaim\Core\Interfaces\CacheInterface;

/**
 * Cache 
*/
class Cache implements CacheInterface
{
    const DEFAULT_DRIVER    = 'void';

    // drivers
    const FILESYSTEM_DRIVER = 'filesystem';
    const APCU_DRIVER       = 'apcu';
    const VOID_DRIVER       = 'void';
    const REDIS_DRIVER      = 'redis';
    const PREDIS_DRIVER     = 'predis';

    /**
     * Drivers list
     *
     * @var array
     */
    protected $drivers = [
        Self::FILESYSTEM_DRIVER => 'Arikaim\Core\Cache\Drivers\FilesystemCache',
        Self::APCU_DRIVER       => 'Arikaim\Core\Cache\Drivers\ApcuCache',
        Self::VOID_DRIVER       => 'Arikaim\Core\Cache\Drivers\VoidCache',
        Self::REDIS_DRIVER      => 'Arikaim\Core\Cache\Drivers\RedisCache',
        Self::PREDIS_DRIVER     => 'Arikaim\Core\Cache\Drivers\PredisCache'
    ];

    /**
     * Cache driver
     *
     * @var Doctrine\Common\Cache\Cache
     */
    protected $driver;
    
    /**
     * Cache directory
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Route cache file
     *
     * @var string
     */
    protected $routeCacheFile;

    /**
     * Default save time
     *
     * @var int
     */
    protected $saveTime;

    /**
     * Constructor
     *
     * @param string $cacheDir       
     * @param string $driverName
     * @param int $saveTime
     * @param array $options
     */
    public function __construct(
        string $cacheDir,      
        string $driverName = 'void',        
        int $saveTime = 7, 
        array $options = []
    )
    {       
        $this->saveTime = $saveTime;      
        $this->cacheDir = $cacheDir;       
        $this->driver = $this->createDriver($driverName,$options);  
    }

    /**
     * Get supported driver names.
     *
     * @return array
     */
    public function getSupportedDrivers(): array
    {
        $result = [];
        foreach ($this->drivers as $name => $class) {
            if ($this->isAvailable($name) == true) {
                $result[$name] = $class;
            }
        }

        return $result;        
    }

    /**
     * Return true if driver name is avaliable
     *
     * @param string $driverName
     * @return boolean
     */
    public function isAvailable(string $driverName): bool
    {
        switch ($driverName) {          
            case Self::APCU_DRIVER: {
                return \extension_loaded('apcu');
            }
            case Self::REDIS_DRIVER: {
                return \class_exists('Redis');
            }
            case Self::PREDIS_DRIVER: {
                return \class_exists('\Predis\Client');
            }
        }

        return true;
    }

    /**
     * Create cache driver
     *
     * @param string $name
     * @param array $options
     * @return Doctrine\Common\Cache\Cache|null
     */
    public function createDriver(string $name, array $options = [])
    {
        $class = (empty($this->drivers[$name] ?? null) == true) ? $this->drivers['void'] : $this->drivers[$name];
      
        switch ($name) {
            case Self::FILESYSTEM_DRIVER:               
                return new $class($this->cacheDir,$options);        
        }
        
        return new $class($options);
    }

    /**
     * Get cache dir
     *
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * Return cache driver
     *
     * @return Doctrine\Common\Cache\Cache
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get cache driver name
     *
     * @return string|null
     */
    public function getDriverName(): ?string
    {
        $found = \array_search(\get_class($this->driver),$this->getSupportedDrivers());

        return ($found === false) ? null : $found;
    }

    /**
     * Set cache driver
     *
     * @param Doctrine\Common\Cache\Cache|string $driver
     * @return void
     */
    public function setDriver($driver, array $options = []): void
    {
        $this->driver = ($driver instanceof CacheInterface) ? $driver : $this->createDriver($driver<$options);
    }

    /**
     * Read item
     *
     * @param string $id
     * @return mixed|false
     */
    public function fetch(string $id)
    {      
        return $this->driver->fetch($id);
    }
    
    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->driver->has($id);
    }

    /**
     * Save cache item
     *
     * @param string $id item id
     * @param mixed $data item data
     * @param integer|null $lifeTime lifetime in minutes
     * @return bool
     */
    public function save(string $id, $data, int $lifeTime = 0): bool
    {
        $lifeTime = (empty($lifeTime) == true) ? $this->saveTime : $lifeTime;
        
        return $this->driver->save($id,$data,$lifeTime * 60000);
    }

    /**
     * Delete cache item
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->driver->delete($id);     
    }

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats(): ?array
    {
        return $this->driver->getStats();
    }

    /**
     * Delete all cache items + views cache files and route cache
     *
     * @return bool
     */
    public function clear(): bool
    {       
        // clear templates cache
        \Arikaim\Core\Utils\File::deleteDirectory($this->cacheDir);

        return $this->driver->clear();
    }

    /**
     * Create cache path.
     *
     * @return bool
     */
    public function createPath(): bool
    {
        if (\is_dir($this->cacheDir) == false) {
            try {
                \mkdir($this->cacheDir,0777,true);

                return (\is_dir($this->cacheDir) !== false);
            } catch (\Exception $e) {
                return false;
            }
        }

        if (\is_writable($this->cacheDir) == false) {
            \chmod($this->cacheDir,0777);
        }

        return true;
    }
}
