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
 * Package actions trait
*/
trait Actions 
{
    /**
     * Get package actions path
     *   
     * @return string
    */
    public function getActionsPath(): string   
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR;
    }

     /**
     * Get package actions
     *
     * @return array
     */
    public function getPackageActions(): array
    {
        $path = $this->getActionsPath();
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
          
            $result[] = \str_replace('.php','',$file->getFilename());
        }

        return $result;
    }
}
