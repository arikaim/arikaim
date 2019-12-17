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
interface PackageManagerFactoryInterface 
{  
    /**
     * Create package manager
     *
     * @param string $packageType
     * @return PackageManagerInterface|null
     */
    public function create($packageType);
}
