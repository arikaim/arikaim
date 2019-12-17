<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Options;

use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Interfaces\OptionsStorageInterface;
use Arikaim\Core\Interfaces\OptionsInterface;

/**
 * Options base class
 */
class Options extends Collection implements OptionsInterface
{ 
    /**
     * Should reload options array
     *
     * @var boolean
     */
    protected $needReload;
    
    /**
     * Storage adapter
     *
     * @var OptionsInterface
     */
    protected $adapter;

    /**
     * Cache
     *
     * @var Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param 
     */
    public function __construct(OptionsStorageInterface $adapter, $cache = null) 
    {  
        $this->cache = $cache;
        $this->adapter = $adapter;
        $this->needReload = true;
        
        parent::__construct([]);       
    }

    /**
     * Store options in collection 
     *
     * @return void
     */
    public function load()
    {
        $options = is_object($this->cache) ? $this->cache->fetch('options') : null;
        if (is_array($options) == false) {        
            $options = $this->adapter->loadOptions();
            is_object($this->cache) ?? $this->cache->save('options',$options,2);
        }
        $this->data = $options;
        $this->needReload = false;
    }

    /**
     * Create option, if option exists return false
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $autoLoad
     * @param string|null $extension
     * @return boolean
    */
    public function createOption($key, $value, $autoLoad = false, $extension = null)
    {
        $result = $this->adapter->createOption($key,$value,$autoLoad,$extension);
        if ($result !== false) {
            $this->data[$key] = $value;
        }

        return $result;
    }

    /**
     * Save option
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $autoLoad
     * @param string $extension
     * @return bool
     */
    public function set($key, $value, $autoLoad = false, $extension = null)
    {
        $result = $this->adapter->saveOption($key,$value,$autoLoad,$extension);
        if ($result !== false) {
            // clear options cache
            if (is_object($this->cache) == true) {
                $this->cache->delete('options');
            }
            $this->data[$key] = $value;
        }

        return $result;
    }

    /**
     * Return true if option name exist
     *
     * @param string $key
     * @return boolean
    */
    public function has($key)
    {
        return $this->adapter->hasOption($key);
    }

    /**
     * Get option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
    */
    public function get($key, $default = null)
    {
        if ($this->needReload == true) {
            $this->load();
        }

        $value = (isset($this->data[$key]) == true) ? $this->data[$key] : $this->adapter->read($key,$default);

        return (Utils::isJson($value) == true) ? json_decode($value,true) : $value;       
    }

    /**
     * Remove option(s)
     *
     * @param string $key
     * @param string|null $extension
     * @return bool
    */
    public function remove($key = null, $extension = null)
    {
        $result = $this->adapter->remove($key,$extension);
        if ($result !== false) {
            $this->needReload = true;           
        }

        return $result;
    }

    /**
     * Search options
     *
     * @param string $searchKey
     * @return array
     */
    public function searchOptions($searchKey)
    {
        return $this->adapter->searchOptions($searchKey);
    }
}
