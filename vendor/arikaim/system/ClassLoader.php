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

/**
 * Class loader
 */
class ClassLoader 
{
    /**
     * Root classes path
     *
     * @var string
     */
    private $rootPath;

    /**
     * Base path
     *
     * @var string
     */
    private $basePath;

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
     * Constructor
     *
     * @param string $basePath
     * @param string $rootPath
     * @param string $corePath
     */
    public function __construct($basePath, $rootPath = null, $coreNamespace = null, $packagesNamespace = []) 
    {   
        $this->rootPath = $rootPath;
        $this->coreNamespace = $coreNamespace;
        $this->basePath = $basePath;
        $this->packagesNamespace = $packagesNamespace;
    }
    
    /**
     * Register loader
     * 
     * @return void
     */
    public function register() 
    {
        spl_autoload_register(array($this,'LoadClassFile'));
    }

    /**
     * Load class file
     *
     * @param string $class
     * @return bool
     */
    public function LoadClassFile($class) 
    {
        $file = $this->getClassFileName($class);
        if (file_exists($file) == true) {
            require $file;
            return true;
        }

        return false;
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getDocumentRoot()
    {
        if ($this->rootPath != null) {
            return $this->rootPath;
        }

        return (php_sapi_name() == "cli") ? __DIR__ : $_SERVER['DOCUMENT_ROOT'];         
    }

    /**
     * Get class file name
     *
     * @param string $class
     * @return string
     */
    public function getClassFileName($class) 
    {   
        $path = $this->getDocumentRoot() . $this->basePath;  
       
        $namespace = $this->getNamespace($class);
        $tokens = explode('\\',$class);
        $class = end($tokens);
        $namespace = $this->namespaceToPath($namespace); 
     
        return $path . DIRECTORY_SEPARATOR .  $namespace . DIRECTORY_SEPARATOR . $class . ".php";       
    }

    /**
     * Get namspace
     *
     * @param string $class
     * @return string
     */
    public function getNamespace($class) 
    {           
        return substr($class,0,strrpos($class,"\\"));       
    } 
    
    /**
     * Convert namespace to path
     *
     * @param string $namespace
     * @param boolean $full
     * @return string
     */
    public function namespaceToPath($namespace, $full = false) 
    {  
        $namespace = ltrim($namespace,'\\');
        $namespace = str_replace($this->coreNamespace,strtolower($this->coreNamespace),$namespace);
    
        foreach ($this->packagesNamespace as $value) {
            if (strpos($namespace,$value) !== false) {
                $namespace = strtolower($namespace);
                break;
            }            
        }
        $namespace = str_replace("\\",DIRECTORY_SEPARATOR,$namespace);
        
        if ($full == true) {
            $path = $this->getDocumentRoot() . $this->basePath;
            $namespace = $path . DIRECTORY_SEPARATOR .  $namespace;
        }
       
        return $namespace;   
    } 

    /**
     *  Load class alias
     *
     * @param string $class
     * @param string $alias
     * @return bool
     */
    public function loadClassAlias($class, $alias)
    {
        return (class_exists($class) == true) ? class_alias($class,$alias) : false;                
    }

    /**
     * Load class aliaeses
     *
     * @param array $items
     * @return bool
     */
    public function loadAlliases(array $items)
    {                
        foreach ($items as $class => $alias) {      
            if ($this->loadClassAlias($class,$alias) == false) { 
                throw new \Exception("Error load class alias for class ($class) alias ($alias)", 1);      
                return false;
            }
        }
        
        return true;
    }
}
