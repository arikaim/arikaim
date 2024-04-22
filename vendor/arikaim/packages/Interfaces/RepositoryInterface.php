<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Interfaces;

/**
 * Repositorydriver interface
 */
interface RepositoryInterface 
{  
    /**
     * Get access key for private repo
     *
     * @return string|null
     */
    public function getAccessKey(): ?string;

    /**
     * Download package
     *
     * @param string|null $version
     * @return bool
     */
    public function download(?string $version = null): bool;

    /**
     * Get package last version
     *
     * @return string|null
     */
    public function getLastVersion(): ?string;

    /**
     * Get package name
     *
     * @return string
     */
    public function getPackageName(): string;

    /**
     * Get repository name
     *
     * @return string
     */
    public function getRepositoryName(): string;

    /**
     * Install repository
     *
     * @param string|null $version
     * @return boolean
     */
    public function install(?string $version = null): bool;

    /**
     * Get repository url
     *
     * @return string
     */
    public function getRepositoryUrl(): string;
}
