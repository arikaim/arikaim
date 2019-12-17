<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Cache;

use Doctrine\Common\Cache\Cache as CacheDriverInterface;
use Doctrine\Common\Cache\FilesystemCache;

use Arikaim\Core\Utils\File;
use Arikaim\Core\Interfaces\CacheInterface;
use Exception;

/**
 * Cache 
*/
class Cache implements CacheInterface
{
    /**
     * Cache driver
     *
     * @var Doctrine\Common\Cache\Cache
     */
    protected $driver;
    
    /**
     * Cache status
     *
     * @var bool
     */
    private $status;

    /**
     * Router cache file name
     *
     * @var string|null
     */
    private $routerCacheFile;

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
     * Constructor
     *
     * @param Doctrine\Common\Cache\Cache $driver
     * @param boolean $status
     * @param string|null $routerCacheFile
     */
    public function __construct($cacheDir, $routerCacheFile, $driver = null, $status = false)
    {
        $this->setStatus($status);
        $this->cacheDir = $cacheDir;
        $this->routerCacheFile = $routerCacheFile;

        $driver = (empty($driver) == true) ? new FilesystemCache($this->cacheDir) : $driver;             
        $this->setDriver($driver);
    }

    /**
     * Set status true - enabled
     *
     * @param boolean $status
     * @return void
     */
    public function setStatus($status)
    {      
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Return true if cache is status
     *
     * @return boolean
     */
    public function isDiabled()
    {
        return (empty($this->status) == true) ? false : !$this->status;
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
     * Set cache driver
     *
     * @param Doctrine\Common\Cache\Cache $driver
     * @throws Exception
     * @return void
     */
    public function setDriver($driver)
    {
        if ($driver instanceof CacheDriverInterface) {
            $this->driver = $driver;
        } else {
            throw new Exception("Error cache driver not valid!", 1);
        }
    }

    /**
     * Read item
     *
     * @param  string $id
     * @return mixed|null
     */
    public function fetch($id)
    {      
        return ($this->isDiabled() == true) ? null : $this->driver->fetch($id);
    }
    
    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->driver->contains($id);
    }

    /**
     * Save cache item
     *
     * @param string $id item id
     * @param mixed $data item data
     * @param integer $lifeTime lifetime in minutes
     * @return bool
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return ($this->isDiabled() == true) ? false : $this->driver->save($id,$data,($lifeTime * 60));
    }

    /**
     * Delete cache item
     *
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        if ($this->driver->contains($id) == true) {
            return $this->driver->delete($id);
        }

        return true;
    }

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats()
    {
        return $this->driver->getStats();
    }

    /**
     * Delete all cache items + views cache files and route cache
     *
     * @return void
     */
    public function clear()
    {
        $this->driver->deleteAll();
        return File::deleteDirectory($this->cacheDir);
    }

    /**
     * Return true if route ceche file exist
     *
     * @return boolean
     */
    public function hasRouteCache()
    {
        return (empty($this->routerCacheFile) == true) ? false : File::exists($this->routerCacheFile);
    }

    /**
     * Delete route cache items and route cache file
     *
     * @return bool
     */
    public function clearRouteCache()
    {
        $this->delete('routes.list');

        return (empty($this->routerCacheFile) == true) ? true : File::delete($this->routerCacheFile);
    }
}
