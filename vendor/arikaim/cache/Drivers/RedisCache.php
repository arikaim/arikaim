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

use \Redis;
use Arikaim\Core\Interfaces\CacheInterface;

/**
 * 
 * Redis cache provider, requires phpredis extension.
 *
 */
class RedisCache implements CacheInterface
{
    /**
     *  Redis instance
     *  @var Redis
     */
    protected $redis;

    /**
     * Driver options
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $serializer = (defined('Redis::SERIALIZER_IGBINARY') && extension_loaded('igbinary')) ? Redis::SERIALIZER_IGBINARY : Redis::SERIALIZER_PHP;
        $this->redis = new Redis();
        $this->redis->setOption(Redis::OPT_SERIALIZER,$serializer);
    }

    /**
     * Gets redis instance.
     *
     * @return Redis
     */
    public function getRedis(): object
    {
        return $this->redis;
    }

    /**
     * Fetch entry.
     *
     * @param string $id 
     * @return mixed|false The cached data or false
    */
    public function fetch(string $id)
    {
        return $this->redis->get($id);
    }

    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        $exists = $this->redis->exists($id);

        return (\is_bool($exists) == true) ? $exists : ($exists > 0);          
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
        return ($lifeTime > 0) ? (bool)$this->redis->setex($id,$lifeTime,$data) : (bool)$this->redis->set($id,$data);          
    }

    /**
     * Delete cache entry.
     *
     * @param string $id cache id.
     * @return bool 
     */
    public function delete(string $id): bool
    {
        return ($this->redis->del($id) >= 0);
    }

    /**
     * Delete all cache items.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->redis->flushDB();
    }

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats(): ?array
    {
        return (array)$this->redis->info(); 
    }
}
