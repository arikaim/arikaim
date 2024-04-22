<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\System;

use Exception;

/**
 * Class loader
 */
class ClassLoader 
{
    /**
     * Arikaim core path
     *
     * @var string
     */
    private $coreNamespace;

    /**
     * Namepaces
     *
     * @var array
     */
    private $packagesNamespace;

    /**
     * Document root
     *
     * @var string
     */
    private $documentRoot;

    /**
     * Path
     *
     * @var string
     */
    private $path;

    /**
     * Constructor
     *
     * @param string $basePath
     * @param string $documentRoot
     * @param string $coreNamespace
     * @param array $packagesNamespace
     */
    public function __construct(string $basePath, string $documentRoot, string $coreNamespace, array $packagesNamespace = []) 
    {        
        $this->coreNamespace = $coreNamespace;
        $this->packagesNamespace = $packagesNamespace;
        $this->documentRoot = $documentRoot;
        $this->path = $this->documentRoot . $basePath;
    }
    
    /**
     * Register loader
     * 
     * @return void
     */
    public function register(): void 
    {
        \spl_autoload_register([$this,'LoadClassFile'],true,false);
    }

    /**
     * Load class file
     *
     * @param string $class
     * @return true|null
     */
    public function LoadClassFile(string $class): ?bool
    {
        $fileName = $this->getClassFileName($class);
        if (\is_file($fileName) == true) {
            require_once ($fileName);
            return true;
        }

        return null;
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getDocumentRoot(): string
    {
        return $this->documentRoot;         
    }

    /**
     * Get class file name
     *
     * @param string $class
     * @return string
     */
    public function getClassFileName(string $class): string 
    {     
        $namespace = \substr($class,0,\strrpos($class,'\\'));     
        $tokens = \explode('\\',$class);
        $class = \end($tokens);
     
        return $this->path . DIRECTORY_SEPARATOR . $this->namespaceToPath($namespace) . DIRECTORY_SEPARATOR . $class . '.php';       
    }

    /**
     * Get namspace
     *
     * @param string $class
     * @return string
     */
    public function getNamespace(string $class): string 
    {           
        return \substr($class,0,\strrpos($class,'\\'));       
    } 
    
    /**
     * Convert namespace to path
     *
     * @param string $namespace
     * @param boolean $full
     * @return string
     */
    public function namespaceToPath(string $namespace, bool $full = false): string 
    {  
        $namespace = \str_replace($this->coreNamespace,\strtolower($this->coreNamespace),\ltrim($namespace,'\\'));
    
        if  (
            \strpos($namespace,$this->packagesNamespace[0]) !== false ||
            \strpos($namespace,$this->packagesNamespace[1]) !== false
            ) {
            
            $namespace = \strtolower($namespace);                             
        }

        $namespace = \str_replace('\\',DIRECTORY_SEPARATOR,$namespace);
         
        return ($full == true) ? $this->path . DIRECTORY_SEPARATOR . $namespace : $namespace;   
    } 

    /**
     *  Load class alias
     *
     * @param string $class
     * @param string $alias
     * @return bool
     */
    public function loadClassAlias(string $class, string $alias): bool
    {
        return (\class_exists($class) == true) ? (bool)\class_alias($class,$alias) : false;                
    }

    /**
     * Load class aliaeses
     *
     * @param array $items
     * @return bool
     */
    public function loadAlliases(array $items): bool
    {                
        foreach ($items as $class => $alias) {      
            if ($this->loadClassAlias($class,$alias) == false) { 
                throw new Exception('Error load class alias for class (' . $class .') alias (' . $alias . ')',1);  
                    
                return false;
            }
        }
        
        return true;
    }
}
