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
 * Storage interface
 */
interface StorageInterface
{    
    /**
     * Get full file path
     *
     * @param tring $path
     * @param string $fileSystemName
     * @return string
     */
    public function getFuillPath($path, $fileSystemName = 'storage');

    /**
     * Write files
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     * @param boolean $dispatchEvent
     * @return bool 
     */
    public function write($path, $contents, $config = [], $dispatchEvent = true);

    /**
     * Read file
     *
     * @param string $path
     * @return string|false
     */
    public function read($path);

    /**
     * Delete file from storage folder
     *
     * @param string $path
     * @param boolean $dispatchEvent
     * @return boolean
    */
    public function delete($path, $dispatchEvent = true);

    /**
     * Rename files
     *
     * @param string $from
     * @param string $to
     * @param boolean $dispatchEvent
     * @return boolean
     */
    public function rename($from, $to, $dispatchEvent = true);

    /**
     * Create directory in storage folder
     *
     * @param string $path
     * @param boolean $dispatchEvent
     * @return boolean
     */
    public function createDir($path, $dispatchEvent = true);

    /**
     * Return true if file exist
     *
     * @param string $path
     * @return boolean
     */
    public function has($path);

    /**
     * Copy files
     *
     * @param string $from
     * @param string $to
     * @param boolean $dispatchEvent
     * @return void
     */
    public function copy($from, $to, $dispatchEvent = true);
}
