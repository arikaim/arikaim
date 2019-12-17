<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Cache interface
 */
interface CacheInterface
{ 
    /**
     * Fetch entry.
     *
     * @param string $id 
     * @return mixed|false The cached data or false
    */
    public function fetch($id);

    /**
     * Save data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime In minutes
     * @return bool  true if data was successfully stored in the cache, false otherwise.
    */
    public function save($id, $data, $lifeTime = 0);

    /**
     * Check if cache contains item
     *
     * @param string $id
     * @return bool
     */
    public function has($id);

    /**
     * Delete cache entry.
     *
     * @param string $id cache id.
     * @return bool 
     */
    public function delete($id);

    /**
     * Return cache stats
     *
     * @return array|null
     */
    public function getStats();

    /**
     * Delete all cache items.
     *
     * @return void
     */
    public function clear();
}
