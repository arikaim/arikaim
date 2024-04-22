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
     * @param string $path
     * @param string|null $fileSystemName
     * @return string
     */
    public function getFullPath(string $path = '', ?string $fileSystemName = null): string;

    /**
     * Write files
     *
     * @param string $path
     * @param string $contents
     * @param array $config
     * @param string|null $fileSystemName
     * @return bool 
     */
    public function write(string $path, $contents, $config = [], ?string $fileSystemName = null): bool;

    /**
     * Read file
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return string|false
    */
    public function read(string $path, ?string $fileSystemName = null);

    /**
     * Delete file from storage folder
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function delete(string $path, ?string $fileSystemName = null): bool;

    /**
     * Rename files
     *
     * @param string $from
     * @param string $to
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function rename(string $from, string $to, ?string $fileSystemName = null): bool;

    /**
     * Create directory in storage folder
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function createDir(string $path, ?string $fileSystemName = null): bool;

    /**
     * Return true if file exist
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
    */
    public function has(string $path, ?string $fileSystemName = null): bool;

    /**
     * Copy files
     *
     * @param string $from
     * @param string $to
     * @param string|null $fileSystemName
     * @return void
    */
    public function copy(string $from, string $to, ?string $fileSystemName = null): bool;

    /**
     * Delete directory in storage folder
     *
     * @param string $path
     * @param string|null $fileSystemName
     * @return boolean
     */
    public function deleteDir(string $path, ?string $fileSystemName = null): bool;
}
