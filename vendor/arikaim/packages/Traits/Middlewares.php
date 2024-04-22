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

use Arikaim\Core\Utils\Factory;
use DirectoryIterator;

/**
 * Package middleware trait
*/
trait Middlewares 
{
    /**
     * Get extension middlewares path
     *   
     * @return string
    */
    public function getMiddlewaresPath(): string   
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'middlewares' . DIRECTORY_SEPARATOR;
    }

     /**
     * Get extension middlewares
     *
     * @return array
     */
    public function getPackageMiddlewares(): array
    {
        $path = $this->getMiddlewaresPath();
        if (\file_exists($path) == false) {
            return [];
        }

        $result = [];
        foreach (new DirectoryIterator($path) as $file) {
            if (
                $file->isDot() == true || 
                $file->isDir() == true ||
                $file->getExtension() != 'php'
            ) continue;
          
            $baseClass = \str_replace('.php','',$file->getFilename());
            $result[] = Factory::getExtensionClassName($this->getName(),'Middlewares\\' . $baseClass);
        }

        return $result;
    }
}
