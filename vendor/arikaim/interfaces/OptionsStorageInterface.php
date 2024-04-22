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
 * Options interface
 */
interface OptionsStorageInterface
{    
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
     * @param bool $autoLoad
     * @return bool
     */
    public function saveOption(string $key, $value, ?string $extension = null, bool $autoLoad = false): bool;

    /**
     * Return true if option name exist
     *
     * @param string $key
     * @return boolean
    */
    public function hasOption(string $key): bool;

    /**
     * Get option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
    */
    public function read(string $key, $default = null);

    /**
     * Remove option(s)
     *
     * @param string $key
     * @param string|null $extension
     * @return bool
    */
    public function remove(?string $key = null, ?string $extension = null): bool;

    /**
     * Search for options
     *
     * @param string $searchKey
     * @param bool $compactKeys
     * @return array
    */
    public function searchOptions(?string $searchKey, bool $compactKeys = false): array;

    /**
     * Load options
     *
     * @return array
    */
    public function loadOptions(): array;

    /**
     * Get extension options
     *
     * @param string $extensioName
     * @return mixed
     */
    public function getExtensionOptions(string $extensioName);
}
