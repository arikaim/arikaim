<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages;

use Arikaim\Core\Packages\Interfaces\PackageInterface;
use Arikaim\Core\Packages\Package;

/**
 * Composer package base class
*/
class ComposerPackage extends Package implements PackageInterface
{
    /**
     * Get installed composer package details
     *
     * @return mixed
     */
    public function getDetails()
    {
        return Composer::getInstalledPackageInfo($this->getName());
    }

    /**
     * Return true if package is installed
     *
     * @return boolean
     */
    public function isInstalled(): bool
    {
        return Composer::isInstalled($this->getName());
    } 

    /**
     * Get Package version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return Composer::getInstalledPackageVersion($this->getName());
    }

    /**
     * Install package.
     *
     * @param boolean|null $primary Primary package replaces routes or other params
     * @return mixed
     */
    public function install(?bool $primary = null)   
    {        
        Composer::updatePackage($this->getName());

        return (bool)Composer::isInstalled($this->getName());
    }

    /**
     * UnInstall package
     *
     * @return bool
     */
    public function unInstall(): bool 
    {      
        Composer::removePackage($this->getName());

        return (Composer::isInstalled($this->getName()) == false);
    }
}
