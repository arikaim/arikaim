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

use Predis\ClientInterface;
use Predis\Client;
use Arikaim\Core\Interfaces\CacheInterface;

/**
 *
 *  Predis cache driver.
 *
 */
class PredisCache implements CacheInterface
{
    /**
     * Redis client instance
     *
     * @var ClientInterface
     */
    protected $redisClient;

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
        $this->redisClient = new Client();
    }

    /**
     * Fetch entry.
     *
     * @param string $id 
     * @return mixed|false The cached data or false
    */
    public function fetch(string $id)
    {
        $result = $this->redisClient->get($id);

        return ($result === null) ? false : \unserialize($result);        
    }

    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return (bool) $this->redisClient->exists($id);
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
        $data = \serialize($data);

        if ($lifeTime > 0) {
            $response = $this->redisClient->setex($id,$lifeTime,$data);
        } else {
            $response = $this->redisClient->set($id,$data);
        }

        return ($response === true || $response == 'OK');
    }

    /**
     * Delete cache entry.
     *
     * @param string $id cache id.
     * @return bool 
     */
    public function delete(string $id): bool
    {
        return ($this->redisClient->del($id) >= 0);
    }

    /**
     * Delete all cache items.
     *
     * @return bool
     */
    public function clear(): bool
    {
        $response = $this->redisClient->flushDb();

        return ($response === true || $response == 'OK');
    }

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats(): ?array
    {
        $info = $this->redisClient->info();     
        unset(
            $info['Keyspace'],
            $info['Modules'],
            $info['Cluster'],
            $info['CPU'],
            $info['Replication'],
            $info['Clients'],
            $info['Persistence'],
            $info['Server']
        );

        return $info;
    }
}
