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
 * APCu cache driver.
 *
 */
class ApcuCache implements CacheInterface
{
    /**
     * Driver options
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Fetch entry.
     *
     * @param string $id 
     * @return mixed|false The cached data or false
    */
    public function fetch(string $id)
    {
        return \apcu_fetch($id);
    }

    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return \apcu_exists($id);
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
        return \apcu_store($id,$data,$lifeTime);
    }

    /**
     * Delete cache entry.
     *
     * @param string $id cache id.
     * @return bool 
     */
    public function delete(string $id): bool
    {
        return (\apcu_delete($id) || ! \apcu_exists($id));
    }

    /**
     * Delete all cache items.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return \apcu_clear_cache();
    }

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats(): ?array
    {
        $info = \apcu_cache_info(true);
        
        return ($info === false) ? null : $info;
    }
}
