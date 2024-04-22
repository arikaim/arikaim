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
 * Css files trait
*/
trait CssFiles 
{   
    /**
     * Get package css path
     *
     * @return string
     */
    public function getCssPath(): string
    {
        return $this->getPath() . $this->getName() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get package css files
     *
     * @return array
     */
    public function getCssFiles(): array
    {      
        $path = $this->getCssPath();
        if (\file_exists($path) == false) {
            return [];
        }        

        $items = [];    
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() == true || $file->isDir() == true) continue;          
            if ($file->getExtension() == 'css') {
                $items[] = $file->getFilename();        
            }               
        }

        return $items;
    }
}
