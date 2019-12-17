<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Extension;

use Arikaim\Core\Interfaces\ExtensionInterface;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Db\Schema;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Utils\Utils;
use Exception;

/**
 * Base class for all extensions.
*/
abstract class Extension implements ExtensionInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    private $consoleClasses = [];

    /**
     * All extensions should implement install method
     *
     * @return mixed
     */
    abstract public function install();
    
    /**
     * UnInstall extension
     *
     * @return boolean
     */
    public function unInstall()
    {
        return true;
    }

    /**
     * Add permission item
     *
     * @param string $name
     * @param string|null $title
     * @param string|null $description
     * @return boolean
     */
    public function addPermission($name, $title = null, $description = null)
    {
        return Arikaim::access()->addPermission($name,$title,$description,$this->getName());        
    }

    /**
     * Add relation map for Polymorphic Relations relations
     *
     * @param string $type
     * @param string $modelClass
     * @return void
     */
    public function addRelationMap($type, $modelClass)
    {
        $relations = Arikaim::config()->load('relations.php');       
        $relations[$type] = Factory::getModelClass($modelClass,$this->getName());
        $relations = array_unique($relations);
      
        return Arikaim::config()->save('relations.php',$relations);
    }

    /**
     * Create extension option
     *
     * @param string $key
     * @param mxied $value
     * @param boolean $autoLoad
     * @return bool
     */
    public function createOption($key, $value, $autoLoad = true)
    {
        return Arikaim::options()->createOption($key, $value, $autoLoad,$this->getName());
    }

    /**
      * Install driver
      *
      * @param string|object $name Driver name, full class name or driver object ref
      * @param string|null $class
      * @param string|null $category
      * @param string|null $title
      * @param string|null $description
      * @param string|null $version
      * @param array $config
      * @return boolean|Model
    */
    public function installDriver($name, $class = null, $category = null, $title = null, $description = null, $version = null, $config = [])
    {
        return Arikaim::driver()->install($name,$class,$category,$title,$description,$version,$config,$this->getName());
    }

    /**
     * Return extension name
     *
     * @return string
     */
    public function getName() 
    {    
        $class = Utils::getBaseClassName($this);
        
        return strtolower($class);      
    }

    /**
     * Return console commands classes
     *
     * @return array
     */
    public function getConsoleCommands()
    {
        return $this->consoleClasses;
    }

    /**
     * Register console command class
     *
     * @param string $class
     * @return bool
     */
    public function registerConsoleCommand($class)
    {
        $class = Factory::getExtensionConsoleClassName($this->getName(),Utils::getBaseClassName($class));
        if (class_exists($class) == false) {
            return false;
        }
        array_push($this->consoleClasses,$class);
        $this->consoleClasses = array_unique($this->consoleClasses);

        return true;
    }

    /**
     * Create job
     *
     * @param string $class
     * @param string|null $name
     * @return JobInterface
     */
    public function createJob($class, $name = null)
    {       
        return Factory::createJob($class,$this->getName(),$name);
    }

    /**
     * Add job to queue
     *
     * @param string $class
     * @param string|null $name
     * @return boolean
     */
    public function addJob($class, $name = null)
    {       
        $job = $this->createJob($class,$name);
        if (is_object($job) == false) {
            return false;
        }

        return Arikaim::queue()->addJob($job,$this->getName());
    }

    /**
     * Register extension event
     *
     * @param string $name Event name
     * @param string $title Event title
     * @param string $description Event description
     * @return bool
     */
    public function registerEvent($name, $title = null, $description = null)
    {
        return Arikaim::event()->registerEvent($name,$title,$this->getName(),$description);
    }

    /**
     * Get extension controller full class name
     *
     * @param string $class
     * @return string
     */
    public function getControllerClassName($class)
    {
        return ((substr($class,0,7) == 'Arikaim') == true) ? $class : Factory::getExtensionControllerClass($this->getName(),$class);       
    }

    /**
     * Register page route
     *
     * @param string $pattern
     * @param string|null $class
     * @param string|null $handlerMethod
     * @param null|integer|string $auth
     * @param string|null $pageName
     * @param string|null $routeName
     * @param boolean $withLanguage
     * @return bool
     */
    public function addPageRoute($pattern, $class = null, $handlerMethod = null, $pageName = null, $auth = null, $routeName = null, $withLanguage = true)
    {
        if (Arikaim::routes()->isValidPattern($pattern) == false) {           
            return false;
        }
        $class = ($class == null) ? Factory::getControllerClass("Controller") : $this->getControllerClassName($class);
        $handlerMethod = ($handlerMethod == null) ? "loadPage" : $handlerMethod;
        $auth = Arikaim::access()->resolveAuthType($auth);
 
        return Arikaim::routes()->addPageRoute($pattern,$class,$handlerMethod,$this->getName(),$pageName,$auth,$routeName,$withLanguage);
    }

    /**
     * Register show page route (handler: PageLoader:loadPage)
     *
     * @param string $pattern
     * @param string $pageName
     * @param null|integer|string $auth
     * @param string|null $routeName
     * @param boolean $withLanguage
     * @return bool
     */
    public function addShowPageRoute($pattern, $pageName, $auth = null, $withLanguage = true, $routeName = null)
    {                  
        if (Arikaim::routes()->isValidPattern($pattern) == false) {
            return false;
        }
        return $this->addPageRoute($pattern,null,"loadPage",$pageName,$auth,$routeName,$withLanguage);
    }

    /**
     * Register api route 
     *
     * @param string $method
     * @param string $pattern
     * @param string $class
     * @param string $handlerMethod
     * @param null|integer|string $auth
     * @return bool
     */
    public function addApiRoute($method, $pattern, $class, $handlerMethod, $auth = null)
    {
        if (Arikaim::routes()->isValidPattern($pattern) == false) {
            return false;
        }
        $auth = Arikaim::access()->resolveAuthType($auth);
        $class = ($class == null) ? Factory::getControllerClass("Controller") : $this->getControllerClassName($class);
        
        return Arikaim::routes()->addApiRoute($method,$pattern,$class,$handlerMethod,$this->getName(),$auth);
    }

    /**
     * Creaete extension db table 
     *
     * @param string $schemaClass
     * @return boolean
     */
    public function createDbTable($schemaClass)
    {       
        return Schema::install($schemaClass,$this->getName());
    }

    /**
     * Drop extension db table
     *
     * @param string $schemaClass
     * @return boolean
     */
    public function dropDbTable($schemaClass)
    {
        return Schema::unInstall($schemaClass,$this->getName());
    }
}
