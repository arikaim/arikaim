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

use Arikaim\Core\Interfaces\OptionsStorageInterface;;

/**
 * Options interface
 */
interface OptionsInterface
{    
    /**
     * Set storage adapter
     *
     * @param OptionsStorageInterface $adapter
     * @return void
     */
    public function setStorageAdapter(OptionsStorageInterface $adapter);

    /**
     * Create option, if option exists return false
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $autoLoad
     * @param string|null $extension
     * @return boolean
    */
    public function createOption(string $key, $value, bool $autoLoad = false, ?string $extension = null): bool;

    /**
     * Save option
     *
     * @param string $key
     * @param mixed $value   
     * @param string|null $extension
     * @return bool
     */
    public function set(string $key, $value, $extension = null);

    /**
     * Get option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
    */
    public function get(string $key, $default = null);

    /**
     * Return true if option name exist
     *
     * @param string $key
     * @return boolean
    */
    public function has(string $key): bool;

    /**
     * Remove option(s)
     *
     * @param string $key
     * @param string|null $extension
     * @return bool
    */
    public function removeOptions($key = null, $extension = null);

    /**
     * Search options
     *
     * @param string $searchKey
     * @param bool $compactKeys
     * @return array
     */
    public function searchOptions($searchKey, $compactKeys = false);

    /**
     * Get extension options
     *
     * @param string $extensioName
     * @return mixed
     */
    public function getExtensionOptions($extensioName);

    /**
     * Force load option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function read(string $key, $default = null);
}
