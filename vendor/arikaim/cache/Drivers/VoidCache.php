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

/**
 * 
 * Void cache driver class
 * 
 */
class VoidCache implements CacheInterface
{
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
    }

    /**
     * Fetch entry.
     *
     * @param string $id 
     * @return mixed|false The cached data or false
    */
    public function fetch(string $id)
    {
        return false;
    }

    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return false;
    }

    /**
     * Save data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime In minutes
     * @return bool  true if data was successfully stored in the cache, false otherwise.
    */
    public function save(string $id, $data, int $lifeTime = 0): bool
    {
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
        return true;
    }

    /**
     * Delete all cache items.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return true;
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
}
