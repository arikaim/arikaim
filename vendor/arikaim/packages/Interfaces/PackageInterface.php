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
 * Package interface, all packages type should impelement it.
 */
interface PackageInterface 
{  
    /**
     * Get drivers classes list
     *
     * @return array
     */
    public function getDrivers(): array;
    
    /**
     * Return true if package is installed
     *
     * @return boolean
     */
    public function isInstalled(): bool;
    
    /**
     * Return package name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get Package version
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Get package type
     *
     * @return string|null
    */
    public function getType(): ?string;

    /**
     * Return package properties
     *
     * @param boolean $full
     * @return Collection
     */
    public function getProperties(bool $full = false);

    /**
     * Validate package properties 
     *
     * @return bool
     */
    public function validate(): bool;

    /**
     * Install package
     *
     * @param boolean|null $primary Primary package replaces routes or other params
     * @return mixed
     */
    public function install(?bool $primary = null);

    /**
     * Run post install actions
     *     
     * @return boolean
     */
    public function postInstall(): bool;
    
    /**
     * Unintsll package
     *
     * @return bool
     */
    public function unInstall(): bool;

    /**
     * Enable package
     *
     * @return bool
     */
    public function enable(): bool;

    /**
     * Disable package
     *
     * @return bool
     */
    public function disable(): bool;

    /**
     * Set package as primary
     *
     * @return boolean
    */
    public function setPrimary(): bool;

    /**
     * Get require property
     *
     * @return CollectionInterface
     */
    public function getRequire();
}
