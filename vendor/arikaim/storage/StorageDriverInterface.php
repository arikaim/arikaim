<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;

/**
 * Storage driver interface
 */
interface StorageDriverInterface
{    
    /**
     * Get filesystem
     *
     * @return Filesystem
     */
    public function getFilesystem();

    /**
     * Get adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * Check filesystem adapter connection
     *
     * @return mixed
     */
    public function checkConnection();

    /**
     * Get root path
     *
     * @return string|null
     */
    public function getRootPath(): ?string;
}
