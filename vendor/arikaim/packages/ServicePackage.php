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
use Arikaim\Core\Collection\Collection;

/**
 * Service package 
*/
class ServicePackage extends Package implements PackageInterface
{
    /**
     * Get package properties
     *
     * @param boolean $full
     * @return Collection
     */
    public function getProperties(bool $full = false)
    {
        $this->properties['icon'] = $this->properties->get('icon',null); 
        if ($full == true) {              
           
        }

        return $this->properties; 
    }

    /**
     * Install service package
     *
     * @param boolean|null $primary Primary package replaces routes or other params
     * @return mixed
     */
    public function install(?bool $primary = null)
    {
        return false;          
    }
    
    /**
     * Uninstall package
     *
     * @return bool
     */
    public function unInstall(): bool 
    {
       return false;
    }

    /**
     * Enable package
     *
     * @return bool
     */
    public function enable(): bool 
    {
        return false;
    }

    /**
     * Disable package
     *
     * @return bool
     */
    public function disable(): bool 
    {
        return false;
    }   
}
