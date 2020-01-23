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
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Routes\Route;

use Arikaim\Core\System\Error\Traits\TaskErrors;

/**
 * Base class for all extensions.
*/
abstract class Extension implements ExtensionInterface
{
    use TaskErrors;

    /**
     * Errors lits
     *
     * @var array
     */
    private $errors;

    /**
     * Primary extension
     *
     * @var boolean
     */
    private $primary;

    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->errors = [];
        $this->primary = false;
    }

    /**
     * Extension console classes 
     *
     * @var array
     */
    private $consoleClasses = [];

    /**
     * All extensions should implement install method
     *
     * @return void
     */
    abstract public function install();
    
    /**
     * UnInstall extension
     *
     * @return void
     */
    public function unInstall()
    {
    }

    /**
     * Load extension json config file
     *
     * @param string $fileName
     * @param string $extensionName
     * @return array
     */
    public static function loadJsonConfigFile($fileName, $extensionName)
    {
        $configDir = Path::EXTENSIONS_PATH . $extensionName . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $data = File::readJsonFile($configDir . $fileName);
        
        return (is_array($data) == true) ? $data : [];
    }

    /**
     * Set extension as primary (override all existing routes)
     *
     * @return void
     */
    public function setPrimary()
    {
        $this->primary = true;
    }

    /**
     * Return true if extension is primary
     *
     * @return boolean
     */
    public function isPrimary()
    {
        return ($this->primary == true);
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
        $result = Arikaim::access()->addPermission($name,$title,$description,$this->getName());     
        if ($result !== true) {
            // add error
            $this->addError(Arikaim::errors()->getError("REGISTER_PERMISSION_ERROR",['name' => $name]));
        }   

        return $result;
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
      
        $result = Arikaim::config()->save('relations.php',$relations);
        if ($result === false) {
            // add error
            $this->addError(Arikaim::errors()->getError("REGISTER_RELATION_ERROR",['class' => $modelClass]));
        }   

        return $result;
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
        $result = Arikaim::options()->createOption($key,$value,$autoLoad,$this->getName());
        if ($result !== true) {
            if (Arikaim::options()->has($key) == false) {
                // add error
                $this->addError(Arikaim::errors()->getError("ERROR_CREATE_OPTION",['key' => $key]));
                return false;
            }   
            return true;
        } 
        
        return $result;
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
        $result = Arikaim::driver()->install($name,$class,$category,$title,$description,$version,$config,$this->getName());
        if ($result !== true) {
            // add error
            $this->addError(Arikaim::errors()->getError("ERROR_INSTALL_DRIVER",['name' => $name]));
        } 
        
        return $result;
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
            // add error
            $this->addError(Arikaim::errors()->getError("NOT_VALID_CONSOLE_CLASS",['class' => $class])); 
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
            $this->addError(Arikaim::errors()->getError("REGISTER_JOB_ERROR",['name' => $name])); 
            return false;
        }

        $result = Arikaim::queue()->addJob($job,$this->getName());
        if ($result !== true) {
            $this->addError(Arikaim::errors()->getError("REGISTER_JOB_ERROR",['name' => $name])); 
        }

        return $result;
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
        $result = Arikaim::event()->registerEvent($name,$title,$this->getName(),$description);
        if ($result !== true) {
            $this->addError(Arikaim::errors()->getError("REGISTER_EVENT_ERROR",['name' => $name])); 
        }

        return $result;
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
     * Register home page route
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
    public function addHomePageRoute($pattern, $class = null, $handlerMethod = null, $pageName = null, $auth = null, $routeName = null, $withLanguage = false)
    {
        if ($this->primary == true) {                     
            Arikaim::routes()->deleteHomePage();           
        } else {
            $homePageRoute = Arikaim::routes()->getRoutes(['type' => 3]); // find home page route
            if (empty($homePageRoute) == false) {
                return true;
            }          
        }

        return $this->addPageRoute($pattern,$class,$handlerMethod,$pageName,$auth,$routeName,$withLanguage,3);
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
     * @param integer $type
     * @return bool
     */
    public function addPageRoute($pattern, $class = null, $handlerMethod = null, $pageName = null, $auth = null, $routeName = null, $withLanguage = false, $type = 1)
    {
        $class = ($class == null) ? Factory::getControllerClass("Controller") : $this->getControllerClassName($class);
        $handlerMethod = ($handlerMethod == null) ? "pageLoad" : $handlerMethod;
        $auth = Arikaim::access()->resolveAuthType($auth);
 
        // if extension is primary remove existing page route
        if ($this->isPrimary() == true) {
            Arikaim::routes()->delete('GET',$pattern);
            Arikaim::routes()->delete('GET',$pattern . Route::getLanguagePattern($pattern));          
        }

        $result = Arikaim::routes()->addPageRoute($pattern,$class,$handlerMethod,$this->getName(),$pageName,$auth,$routeName,$withLanguage,$type);
        if ($result !== true) {           
            $this->addError(Arikaim::errors()->getError("REGISTER_ROUTE_ERROR",['pattern' => $pattern])); 
        }

        return $result;
    }

    /**
     * Register show page route
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
        return $this->addPageRoute($pattern,null,"pageLoad",$pageName,$auth,$routeName,$withLanguage);
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
        $auth = Arikaim::access()->resolveAuthType($auth);
        $class = ($class == null) ? Factory::getControllerClass("Controller") : $this->getControllerClassName($class);
        
        $result = Arikaim::routes()->addApiRoute($method,$pattern,$class,$handlerMethod,$this->getName(),$auth);
        if ($result !== true) {
            $this->addError(Arikaim::errors()->getError("REGISTER_ROUTE_ERROR",['pattern' => $pattern])); 
        }

        return $result;
    }

    /**
     * Creaete extension db table 
     *
     * @param string $schemaClass
     * @return boolean
     */
    public function createDbTable($schemaClass)
    {       
        $result = Schema::install($schemaClass,$this->getName());
        if ($result !== true) {
            $this->addError(Arikaim::errors()->getError("CREATE_DB_TABLE_ERROR",['class' => $schemaClass])); 
        }

        return $result;
    }

    /**
     * Drop extension db table
     *
     * @param string $schemaClass
     * @return boolean
     */
    public function dropDbTable($schemaClass)
    {
        $result = Schema::unInstall($schemaClass,$this->getName());
        if ($result !== true) {
            $this->addError(Arikaim::errors()->getError("DROP_DB_TABLE_ERROR",['class' => $schemaClass])); 
        }

        return $result;
    }
}
