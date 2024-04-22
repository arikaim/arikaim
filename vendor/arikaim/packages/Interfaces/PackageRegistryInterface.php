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
 * Package Registry Interface
 */
interface PackageRegistryInterface 
{  
    /**
     * Get package
     * 
     * @param string $name
     * @return array|false
     */
    public function getPackage(string $name);

    /**
     * Add package
     * 
     * @param string $name
     * @param array $data
     * @return boolean
     */
    public function addPackage(string $name, array $data): bool;

    /**
     * Remove Package
     * 
     * @param string $name
     * @return boolean
     */
    public function removePackage(string $name): bool;

    /**
     * Get package list
     *
     * @param array $filter
     * @return array
    */
    public function getPackagesList(array $filter = []);

    /**
     * Return true if package is installed
     *
     * @param string $name
     * @return boolean
     */
    public function hasPackage(string $name): bool;

    /**
     * Set package status
     *
     * @param string $name
     * @param integer|string $status
     * @return boolean
    */
    public function setPackageStatus(string $name, $status): bool;

    /**
     * Get package status
     *
     * @param string $name
     * @return integer|null
    */
    public function getPackageStatus(string $name): ?int;

    /**
     * Set package as primary
     *
     * @param string $name
     * @return boolean
    */
    public function setPrimary(string $name): bool;

    /**
     * Return true if package is primary.
     *  
     * @param string $name
     * @return boolean|null
    */
    public function isPrimary(string $name): ?bool;
}
