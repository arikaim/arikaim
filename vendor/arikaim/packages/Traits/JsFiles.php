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
 * Js files trait
*/
trait JsFiles 
{   
    /**
     * Get package js path
     *
     * @return string
     */
    public function getJsPath(): string
    {
        return $this->getPath() . $this->getName() . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get package js files
     *
     * @return array
     */
    public function getJsFiles(): array
    {      
        $path = $this->getJsPath();
        if (\file_exists($path) == false) {
            return [];
        }        

        $items = [];    
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() == true || $file->isDir() == true) continue;          
            if ($file->getExtension() == 'js') {
                $items[] = $file->getFilename();        
            }               
        }

        return $items;
    }
}
