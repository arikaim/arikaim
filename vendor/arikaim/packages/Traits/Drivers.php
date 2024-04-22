<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Traits;

/**
 * Get package drivers.
*/
trait Drivers 
{
    /**
     * Get module console commands class list.
     *
     * @return array
     */
    public function getDrivers(): array
    { 
        $path = $this->getDriversPath();
        if (\file_exists($path) == false) {
            return [];
        }

        $result = [];
        foreach (new \DirectoryIterator($path) as $file) {
            if (
                $file->isDot() == true || 
                $file->isDir() == true ||
                $file->getExtension() != 'php'
            ) continue;
         
            $fileName = $file->getFilename();
            $baseClass = \str_replace('.php','',$fileName);            
            $result[] = $baseClass;
        }     
        
        return $result;
    }

    /**
     * Get module drivers path
     *    
     * @return string
    */
    public function getDriversPath(): string
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'drivers' . DIRECTORY_SEPARATOR;
    }
}
