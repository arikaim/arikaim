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

use Arikaim\Core\Packages\Package;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Packages\Interfaces\PackageInterface;
use Arikaim\Core\Interfaces\ModuleInterface;

use Arikaim\Core\Packages\Traits\ConsoleCommands;
use Arikaim\Core\Packages\Traits\Drivers;
use Arikaim\Core\Packages\Traits\Jobs;
use Exception;

/**
 * Module Package class
*/
class ModulePackage extends Package implements PackageInterface
{
    use Drivers, 
        Jobs,
        ConsoleCommands;

    const SERVICE = 0;
    const PACKAGE = 1;
    const MIDDLEWARE = 2; 

    /**
     * Module type
     */
    const TYPE_NAME = ['service','package','middleware'];

    /**
     * Get module class
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->properties->get('class',ucfirst($this->getName()));
    }

    /**
     * Get module package properties
     *
     * @param boolean $full
     * @return Collection
     */
    public function getProperties(bool $full = false)
    {
        // set default values
        $this->properties['type'] = $this->properties->get('type','service');
        $this->properties['bootable'] = $this->properties->get('bootable',false);
        $this->properties['service_name'] = $this->properties->get('service_name',$this->properties->get('name'));

        if ($full == true) {          
            $this->properties->set('installed',$this->packageRegistry->hasPackage($this->getName()));
            $this->properties->set('status',$this->packageRegistry->getPackageStatus($this->getName()));
            $this->properties['console_commands'] = $this->getConsoleCommands();
            $this->properties['drivers'] = $this->getDrivers();
            $this->properties['jobs'] = $this->getPackageJobs();
            
            $service = Factory::createModule($this->getName(),$this->getClass());
            $error = ($service == null) ? false : $service->getTestError();
            $this->properties->set('error',$error);            
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
     * Install module
     *
     * @param boolean|null $primary Primary package replaces routes or other params
     * @throws Exception
     * @return mixed
     */
    public function install(?bool $primary = null)
    {
        $data = $this->properties->toArray();
      
        $module = Factory::createModule($this->getName(),$this->getClass());
        if ($module == null) {
            throw new Exception('Not valid module class.');
            return false;
        }
       
        if ($module instanceof ModuleInterface) {
            $module->setModuleName($this->getName());
        } else {
            throw new Exception('Not valid module class type.');  
            return false;          
        }

        // Bind methods
        /**
         *  Install driver
         */
        $module->installDriver = function(
            string $name,
            ?string $class = null,
            ?string $category = null,
            ?string $title = null,
            ?string $description = null,
            ?string $version = null,
            array $config = []
        ) use ($module)
        {
            global $arikaim;

            return $arikaim->get('driver')->install(
                $name,
                $class,
                $category,
                $title,
                $description,
                $version,
                $config,
                $module->getModuleName(),
                'module');
        };

        /**
         * Register service provider
         *
         * @param string $serviceProvider
         * @return boolean
         */
        $module->registerService = function(string $serviceProvider): bool
        {
            global $arikaim;

            if (\class_exists($serviceProvider) == false) {
                $serviceProvider = Factory::getModuleNamespace($this->getModuleName()) . "\\Service\\$serviceProvider";
            }

            return (bool)$arikaim->get('service')->register($serviceProvider);            
        };

        /**
         * Register console command class
         *
         * @param string $class
         * @return bool
         */
        $module->registerConsoleCommand = function(string $class)
        {
            $class = Factory::getModuleConsoleClassName($this->getModuleName(),Utils::getBaseClassName($class));
            if (\class_exists($class) == false) {       
                return false;
            }
            $this->addConsoleClass($class);
          
            return true;
        };

        $module->install();

        unset($data['requires'],$data['help'],$data['facade']);
    
        $details = [
            'facade_class'      => $this->properties->getByPath('facade/class',null),
            'facade_alias'      => $this->properties->getByPath('facade/alias',null),
            'type'              => Self::getTypeId($this->properties->get('type')),
            'category'          => $this->properties->get('category',null),
            'class'             => $this->getClass(),
            'console_commands'  => $module->getConsoleCommandClasses()
        ];
        $data = \array_merge($data,$details);
        $result = $this->packageRegistry->addPackage($this->getName(),$data);

        return ($result !== false);
    }

    /**
     * Uninstall module
     *
     * @return bool
     */
    public function unInstall(): bool 
    {
        $result = $this->packageRegistry->removePackage($this->getName());

        return ($result !== false);
    }

    /**
     * Enable module
     *
     * @return bool
     */
    public function enable(): bool 
    {
        return $this->packageRegistry->setPackageStatus($this->getName(),1); 
    }

    /**
     * Disable module
     *
     * @return bool
     */
    public function disable(): bool 
    {
        return $this->packageRegistry->setPackageStatus($this->getName(),0);  
    }   

    /**
     * Get type id
     *
     * @param string $typeName
     * @return integer
     */
    public static function getTypeId($typeName)
    {
        return \array_search($typeName,Self::TYPE_NAME);
    }

    /**
     * Get module console commands path
     *    
     * @return string
     */
    public function getConsolePath(): string
    {
        return $this->path . $this->getName() . DIRECTORY_SEPARATOR . 'console' . DIRECTORY_SEPARATOR;
    }
}
