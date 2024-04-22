<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Packages;

/**
 * Package manager interface
 */
interface PackageManagerInterface 
{  
    /**
     * Create package obj
     *
     * @param string $name
     * @return Arikaim\Core\Packages\Interfaces\PackageInterface
     */
    public function createPackage(string $name);

    /**
     * Find package
     *
     * @param string $param
     * @param mixed $value
     * @return PackageInterface|false
     */
    public function findPackage(string $param, $value);

    /**
     * Get package properties
     *
     * @param string $name
     * @param boolean $full
     * @return Collection|null
    */
    public function getPackageProperties(string $name, bool $full = false);
    
    /**
     * Install package
     *
     * @param string $name
     * @return mixed
     */
    public function installPackage(string $name);

     /**
     * Run post install actions on package
     *
     * @param string $name
     * @return mixed
     */
    public function postInstallPackage(string $name);

    /**
     * Uninstall package
     *
     * @param string $name
     * @return bool
     */
    public function unInstallPackage(string $name): bool;

    /**
     * Enable package
     *
     * @param string $name
     * @return bool
     */
    public function enablePackage(string $name): bool;

    /**
     * Disable package
     *
     * @param string $name
     * @return bool
     */
    public function disablePackage(string $name): bool;
    
    /**
     *  Install all packages 
     *  @return bool
     */
    public function installAllPackages();

    /**
     * Get packages list
     *
     * @param boolean $cached
     * @param mixed $filter
     * @return mixed
     */
    public function getPackages(bool $cached = false, $filter = null);

    /**
     * Return installed packages
     *
     * @param mixed|null $status
     * @param mixed|null $type
     * @return array
     */
    public function getInstalled($status = null, $type = null);

    /**
     * Create zip arhive with package files and save to storage/backup/
     *
     * @param string $name
     * @return boolean
     */
    public function createBackup(string $name): bool;

    /**
     * Get package repository
     *
     * @param string $packageName
     * @return RepositoryInterface
    */
    public function getRepository(string $packageName);
}
