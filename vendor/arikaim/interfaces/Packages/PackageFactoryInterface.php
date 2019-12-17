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
 * Package factory interface
 */
interface PackageFactoryInterface 
{  
    /**
     * Create package
     *
     * @param string $packageType
     * @param string $name
     * @return PackageInterface|null
    */
    public function createPackage($packageType, $name);
}
