<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages;

use Arikaim\Core\Packages\Interfaces\PackageInterface;
use Arikaim\Core\Packages\Interfaces\ViewComponentsInterface;
use Arikaim\Core\Packages\Package;
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Packages\Traits\ViewComponents;
use Arikaim\Core\Packages\Traits\Drivers;
use Arikaim\Core\Packages\Traits\ConsoleCommands;
use Arikaim\Core\Packages\Traits\Jobs;
use Arikaim\Core\Packages\Traits\Actions;
use Arikaim\Core\Packages\Traits\Middlewares;
use DirectoryIterator;

/**
 * Extension Package
*/
class ExtensionPackage extends Package implements PackageInterface, ViewComponentsInterface
{
    use ViewComponents,
        ConsoleCommands,
        Jobs,
        Actions,
        Middlewares,
        Drivers;

    /**
     *  Extension type
     */
    const USER   = 0;
    const SYSTEM = 1;

    /**
     * Extension types
     *
     * @var array
     */
    private $typeName = ['user','system'];

    /**
     * Get extension package properties
     *
     * @param boolean $full
     * @return Collection
     */
    public function getProperties(bool $full = false)
    {
        global $arikaim;

        $type = $this->properties->get('type','user');
        $this->properties['type'] = $this->getTypeId($type);
        $this->properties['class'] = (empty($this->properties['class']) == true) ? ucfirst($this->getName()) : $this->properties['class'];       
        $this->properties['installed'] = $this->packageRegistry->hasPackage($this->getName());       
        $this->properties['status'] = $this->packageRegistry->getPackageStatus($this->getName());
        $this->properties['admin_menu'] = $this->properties->get('admin-menu',null);

        // resolve primary extension type
        $primary = $this->properties->get('primary',null);
        $this->properties['primary'] = (empty($primary) == true) ? $this->packageRegistry->isPrimary($this->getName()) : (bool)$primary;

        if ($full == true) { 
            $this->properties['routes'] = $arikaim->get('routes')->getRoutes(['extension_name' => $this->getName()]);
            $this->properties['events'] = $arikaim->get('event')->getEvents(['extension_name' => $this->getName()]);
            $this->properties['subscribers'] = $arikaim->get('event')->getSubscribers(null,$this->getName());
            $this->properties['database'] = $this->getModels();
            $this->properties['console_commands'] = $this->getConsoleCommands();
            $this->properties['jobs'] = $this->getPackageJobs();
            $this->properties['actions'] = $this->getPackageActions();
            $this->properties['pages'] = $this->getPages();
            $this->properties['emails'] = $this->getEmails();
            $this->properties['components'] = $this->getComponentsRecursive();
            $this->properties['macros'] = $this->getMacros();
        }
        
        return $this->properties; 
    }

    /**
     * Return true if package is installed
     *
     * @return boolean
     */
    public function isInstalled(): bool
    {
        return $this->packageRegistry->hasPackage($this->getName());
    } 

    /**
     * Set package as primary
     *
     * @return boolean
     */
    public function setPrimary(): bool
    {
        $result = $this->packageRegistry->setPrimary($this->getName());            
      
        return (bool)$result;       
    }

    /**
     * Get extension models.
     *
     * @return array
     */
    public function getModels(): array
    {      
        $path = $this->getModelsSchemaPath();
        if (File::exists($path) == false) {
            return [];
        }

        $result = [];
        foreach (new DirectoryIterator($path) as $file) {
            if (
                $file->isDot() == true || 
                $file->isDir() == true ||
                $file->getExtension() != 'php'
            ) continue;
         
            $fileName = $file->getFilename();
            $baseClass = \str_replace('.php','',$fileName);
            $schema = Factory::createSchema($baseClass,$this->getName());

            if (\is_subclass_of($schema,'Arikaim\Core\Db\Schema') == true) {               
                $item['name'] = $schema->getTableName();               
                $result[] = $item;
            }
        }    

        return $result;
    }

    /**
     * Install extension package
     *
     * @param boolean|null $primary Primary package replaces routes or other params
     * @return mixed|true
     */
    public function install(?bool $primary = null)
    {
        global $arikaim;

        $details = $this->getProperties(false);
        $extensionName = $this->getName();
        $extObj = Factory::createExtension($extensionName,$details->get('class'));
        if ($extObj == null) {
            return false;
        }
        
        $primary = (empty($primary) == true) ? $details['primary'] : $primary;
        // check for primary 
        if ($primary === true) {
            $extObj->setPrimary();
        }

        // delete extension routes
        $arikaim->get('routes')->deleteRoutes(['extension_name' => $extensionName]);

        // delete registered events subscribers
        $arikaim->get('event')->deleteSubscribers(['extension_name' => $extensionName]);

        // run install extension      
        $extObj->install(); 
      
        // get console commands classes
        $details->set('console_commands',$extObj->getConsoleCommands());
      
        // register events subscribers        
        $this->registerEventsSubscribers();
                   
        $details->set('status',1);

        $this->packageRegistry->addPackage($extensionName,$details->toArray());
        
        return ($extObj->hasError() == false) ? true : $extObj->getError();  
    }

    /**
     * Run post install actions
     *     
     * @return boolean
     */
    public function postInstall(): bool
    {
        $details = $this->getProperties(false);
        $extensionName = $this->getName();
        $extObj = Factory::createExtension($extensionName,$details->get('class'));
        if ($extObj == null) {
            return false;
        }

        // run install extension      
        $extObj->postInstall();

        return true;        
    }

    /**
     * Uninstall extension package
     *
     * @return bool
     */
    public function unInstall(): bool 
    { 
        global $arikaim;

        $details = $this->getProperties(true);
        $extensionName = $this->getName();
        $extObj = Factory::createExtension($extensionName,$details->get('class'));
        
        // delete registered routes
        $arikaim->get('routes')->deleteRoutes(['extension_name' => $extensionName]);

        // delete registered events
        $arikaim->get('event')->deleteEvents(['extension_name' => $extensionName]);

        // delete registered events subscribers
        $arikaim->get('event')->deleteSubscribers(['extension_name' => $extensionName]);

        // delete extension options
        $arikaim->get('options')->removeOptions(null,$extensionName);

        // delete jobs from queue
        $arikaim->get('queue')->deleteJobs(['extension_name' => $extensionName]);
    
        // delete jobs from registry
        $arikaim->get('queue')->jobsRegistry()->deleteJobs($extensionName,'extension');
        
        // run extension unInstall
        $extObj->unInstall();        
        $this->packageRegistry->removePackage($extensionName);

        return ($extObj->hasError() == false);  
    }

    /**
     * Enable extension
     *
     * @return bool
     */
    public function enable(): bool 
    {
        global $arikaim;

        $name = $this->getName();
        $this->packageRegistry->setPackageStatus($name,1);

        // enable extension routes
        $arikaim->get('routes')->setRoutesStatus(['extension_name' => $name],1);

        // enable extension events
        $arikaim->get('event')->setEventsStatus(['extension_name' => $name],1);  

        return true;
    }

    /**
     * Disable extension
     *
     * @return bool
     */
    public function disable(): bool 
    {
        global $arikaim;

        $name = $this->getName();
        $this->packageRegistry->setPackageStatus($name,0);

        // disable extension routes
        $arikaim->get('routes')->setRoutesStatus(['extension_name' => $name],0);         
        
        // disable extension events
        $arikaim->get('event')->setEventsStatus(['extension_name' => $name],0);  
        
        return true;
    }   

    /**
     * Register event subscribers
     *
     * @return integer
     */
    public function registerEventsSubscribers(): int
    {
        global $arikaim;

        $count = 0;
        $name = $this->getName();
        $path = $this->getSubscribersPath($name);       
        if (File::exists($path) == false) {
            return $count;
        }

        foreach (new DirectoryIterator($path) as $file) {
            if (($file->isDot() == true) || ($file->isDir() == true)) continue;
            if ($file->getExtension() != 'php') continue;
            
            $baseClass = \str_replace('.php','',$file->getFilename());
            // add event subscriber to db table
            $result = $arikaim->get('event')->registerSubscriber($baseClass,$name);
            $count += ($result == true) ? 1 : 0;
        }     

        return $count;
    }

    /**
     * Return extension type id
     *
     * @param string|integer $typeName
     * @return integer|false
     */
    public function getTypeId($typeName)
    {
        return (\is_string($typeName) == true) ? \array_search($typeName,$this->typeName) : $typeName;          
    }

    /**
     * Get extension models schema path
     *  
     * @return string
     */
    public function getModelsSchemaPath(): string   
    {
        return $this->getModelsPath() . 'schema' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get extension model path
     *   
     * @return string
     */
    public function getModelsPath(): string   
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get extension subscribers path.
     *    
     * @return string
     */
    public function getSubscribersPath(): string   
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'subscribers' . DIRECTORY_SEPARATOR;
    }
}
