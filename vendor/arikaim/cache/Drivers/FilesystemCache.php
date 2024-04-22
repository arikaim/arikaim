<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Cache
*/
namespace Arikaim\Core\Cache\Drivers;

use Arikaim\Core\Interfaces\CacheInterface;
use Exception;

/**
 * Filesystem cache driver.
 */
class FilesystemCache implements CacheInterface
{
    
    const FILE_EXTENSION = '.cache';

    /**
     * Cache directory
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Driver options
     *
     * @var array
     */
    protected $options;

    /**
     * File umask
     *
     * @var mixed
     */
    protected $umask;

    /**
     * Constructor
     *
     * @param string  $cacheDir
     * @param array $options
     * @param integer $umask
     */
    public function __construct(string $cacheDir, array $options = [], $umask = 0002)
    {
        $this->options = $options;
        $this->cacheDir = $cacheDir;
        $this->umask = $umask;
    }

    /**
     * Fetch entry.
     *
     * @param string $id 
     * @return mixed|false The cached data or false
    */
    public function fetch(string $id)
    {
        $filename = $this->getFilename($id);

        try {
            if (\is_file($filename) == false) {
                return false;
            }
            $fileTime = \filemtime($filename);
            if ($fileTime !== false && $fileTime < time()) {
                return false;
            }

            $data = \file_get_contents($filename);
            
            return ($data === false) ? false : \unserialize($data);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        $filename = $this->getFilename($id);
        if (\is_file($filename) == false) {
            return false;
        }

        $fileTime = \filemtime($filename);

        return ($fileTime === false) ? false : ($fileTime > time());           
    }

    /**
     * Save data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime
     * @return bool  true if data was successfully stored in the cache, false otherwise.
    */
    public function save(string $id, $data, int $lifeTime = 0): bool
    {
        $lifeTime = \time() + $lifeTime;
        $data = \serialize($data);
        $filename = $this->getFilename($id);

        try {
            $result = \file_put_contents($filename,$data);
            if ($result === false) {
                return false;
            }
            \chmod($filename,0666 & (~$this->umask));
            \touch($filename,$lifeTime);
        } catch (Exception $e) {
            return false;
        }
    
        return true;
    }

    /**
     * Delete cache entry.
     *
     * @param string $id cache id.
     * @return bool 
     */
    public function delete(string $id): bool
    {
        try {
            $filename = $this->getFilename($id);
            $result = \unlink($filename);

            return ($result !== false);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete all cache items.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return \Arikaim\Core\Utils\File::deleteDirectory($this->cacheDir);
    }

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats(): ?array
    {
        return null;
    }

    /**
     * Get cache file name
     * 
     * @param string $id
     * @return string
     */
    protected function getFilename(string $id): string
    {  
        return $this->cacheDir . \md5($id) . SELF::FILE_EXTENSION;
    }
}
