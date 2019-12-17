<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Db\Schema;
use Arikaim\Core\Db\Model;
use Arikaim\Core\View\Template\Template;
use Arikaim\Core\Access\Access;
use Arikaim\Core\Queue\Cron;
use Arikaim\Core\System\System;

/**
 * Arikaim install
 */
class Install 
{
    /**
     * Check for install page url
     *
     * @return boolean
     */
    public static function isInstallPage()
    {
        $uri = (isset($_SERVER['REQUEST_URI']) == true) ? $_SERVER['REQUEST_URI'] : '';

        return (substr($uri,-7) == 'install');
    }

    /**
     * Install Arikaim
     *
     * @return boolean;
     */
    public function install() 
    {    
        // clear errors before start
        Arikaim::errors()->clear();

        // create database if not exists  
        $databaseName = Arikaim::config()->getByPath('db/database');
      
        if (Arikaim::db()->has($databaseName) == false) {
            $charset = Arikaim::config()->getByPath('db/charset'); 
            $collation = Arikaim::config()->getByPath('db/collation');
            $result = Arikaim::db()->createDb($databaseName,$charset,$collation); 
            if ($result == false) {
                Arikaim::errors()->addError('DB_DATABASE_ERROR');
                return false;
            }            
        }          

        Arikaim::db()->initConnection(Arikaim::config()->get('db'));     

        // Create Arikaim DB tables
        $result = $this->createDbTables();      
        if ($result == false) {
            return false;
        }

        // add control panel permisison item 
        Arikaim::access()->addPermission(Access::CONTROL_PANEL,Access::CONTROL_PANEL,'Arikaim control panel access.');
      
        // register core events
        $this->registerCoreEvents();

        // reload seystem options
        Arikaim::options()->load();

        // create admin user if not exists 
        $this->createDefaultAdminUser();

        // add date, time, number format items
        $this->initDefaultOptions();

        // install drivers
        $this->installDrivers();

        // install current template 
        $templateManager = Arikaim::packages()->create('template');
        $currentTemplate = $templateManager->findPackage('current',true);
        if ($currentTemplate !== false) {
            $result = $currentTemplate->install();
        }
      
        // Install core modules
        $modulesManager = Arikaim::packages()->create('module');
        $result = $modulesManager->installAllPackages();

        // Install extensions      
        $extensionManager = Arikaim::packages()->create('extension');
        $result = $extensionManager->installAllPackages();
      
        // Install cron scheduler 
        $cron = new Cron();
        $cron->install();
        
        // trigger event core.install
        Arikaim::event()->dispatch('core.install',Arikaim::errors()->getErrors());

        return true;
    } 

    /**
     * Register code events
     *
     * @return void
     */
    private function registerCoreEvents()
    {
        Arikaim::event()->registerEvent('core.extension.update','After update extension.');
        Arikaim::event()->registerEvent('core.extension.download','After download extension.');
        // Routes
        Arikaim::event()->registerEvent('core.route.disable','After disable route.');
        // UI Library
        Arikaim::event()->registerEvent('core.library.download','After download UI Library.');
        // System
        Arikaim::event()->registerEvent('core.install','After install.');
        Arikaim::event()->registerEvent('core.update','After update.');
        // Jobs
        Arikaim::event()->registerEvent('core.jobs.before.execute','Before run job.');
        Arikaim::event()->registerEvent('core.jobs.after.execute','After run job.');
        Arikaim::event()->registerEvent('core.jobs.queue.run','After run jobs queue.');
        // Storage events
        Arikaim::event()->registerEvent('core.storage.delete.file','File is deleted in storage folder.');
        Arikaim::event()->registerEvent('core.storage.write.file','File is added to storage folder.');
        Arikaim::event()->registerEvent('core.storage.update.file','Update File.');
        Arikaim::event()->registerEvent('core.storage.rename.file','Rename file.');
        Arikaim::event()->registerEvent('core.storage.copy.file','Copy file.');
        Arikaim::event()->registerEvent('core.storage.create.dir','Create directory');
        Arikaim::event()->registerEvent('core.storage.delete.dir','Delete directory');
    } 

    /**
     * Create default control panel user
     *
     * @return void
     */
    private function createDefaultAdminUser()
    {
        $user = Model::Users()->getControlPanelUser();
        if ($user == false) {
            $user = Model::Users()->createUser("admin","admin");  
            if (empty($user->id) == true) {
                Arikaim::errors()->addError('CONTROL_PANEL_USER_ERROR',"Error create control panel user.");
                return false;
            }    
        }
        
        return Model::PermissionRelations()->setUserPermission(Access::CONTROL_PANEL,Access::FULL,$user->id);
    }

    /**
     * Set default options
     *
     * @return void
     */
    private function initDefaultOptions()
    {
        // add date formats options
        $items = Arikaim::config()->loadJsonConfigFile("date-format.json");      
        Arikaim::options()->createOption('date.format.items',$items,false);
        // set default date format 
        $key = array_search(1,array_column($items,'default'));
        if ($key !== false) {
            Arikaim::options()->createOption('date.format',$items[$key]['value'],true);
        }
     
        // add time format options
        $items = Arikaim::config()->loadJsonConfigFile("time-format.json");
        Arikaim::options()->createOption('time.format.items',$items,false);
        // set default time format
        $key = array_search(1,array_column($items,'default'));
        if ($key !== false) {
            Arikaim::options()->createOption('time.format',$items[$key]['value'],true);
        }

        // add number format options
        $items = Arikaim::config()->loadJsonConfigFile("number-format.json");
        Arikaim::options()->createOption('number.format.items',$items,false);
        
        // set default time zone 
        Arikaim::options()->createOption('time.zone',DateTime::getTimeZoneName(),false);
        // set default template name
        Arikaim::options()->createOption('current.template',Template::getTemplateName(),true);
        // mailer
        Arikaim::options()->createOption('mailer.use.sendmail',true,true);
        Arikaim::options()->createOption('mailer.smpt.port','25',true);
        Arikaim::options()->createOption('mailer.smpt.host','',true);
        Arikaim::options()->createOption('mailer.username','',true);
        Arikaim::options()->createOption('mailer.password','',true);
        // email settings
        Arikaim::options()->createOption('mailer.from.email','',false);
        // logger
        Arikaim::options()->createOption('logger',true,true);
        Arikaim::options()->createOption('logger.stats',true,true);
        Arikaim::options()->createOption('logger.driver',null,true);
        // session
        Arikaim::options()->createOption('session.recreation.interval',0,false);
        // cachek drivers
        Arikaim::options()->createOption('cache.driver',null,true);
        // library params
        Arikaim::options()->createOption('library.params',[],true);
    }

    /**
     * Install drivers
     *
     * @return void
     */
    public function installDrivers()
    {
        // cache
        Arikaim::driver()->install('filesystem','Doctrine\\Common\\Cache\\FilesystemCache','cache','Filesystem cache','Filesystem cache driver','1.8.0',[]);
    }

    /**
     * Create core db tables
     *
     * @return bool
     */
    private function createDbTables()
    {                 
        $classes = $this->getSystemSchemaClasses();
        $errors = 0;     

        foreach ($classes as $class) {            
            $installed = Schema::install($class);
            if ($installed == false) {
                $errors++;       
                Arikaim::errors()->addError('Error create database table "' . Schema::getTable($class) . '"');
                echo "err: " . Schema::getTable($class);
                exit();
            } 
        }

        return ($errors == 0);   
    }

    /**
     * Check if system is installed.
     *
     * @return boolean
     */
    public static function isInstalled() 
    {
        $errors = 0;      
        try {
            // check db
            $errors += Arikaim::db()->has(Arikaim::config()->getByPath('db/database')) ? 0 : 1;
            if ($errors > 0) {
                return false;
            }
            // check db tables
            $tables = Self::getSystemDbTableNames();
            foreach ($tables as $tableName) {
                $errors += Schema::hasTable($tableName) ? 0:1;
            }
           
            $result = Model::Users()->hasControlPanelUser();                          
            if ($result == false) {
                $errors++;
            }
           
        } catch(\Exception $e) {
            $errors++;
        }

        return ($errors == 0);   
    }

    /**
     * Verify system requirements
     *
     * @return array
     */
    public static function checkSystemRequirements()
    {
        $info['items'] = [];
        $info['errors']['messages'] = "";
        $errors = [];

        // php 5.6 or above
        $phpVersion = System::getPhpVersion();
        $item['message'] = "PHP $phpVersion";
        $item['status'] = 0; // error   
        if (version_compare($phpVersion,'7.1','>=') == true) {               
            $item['status'] = 1; // ok                    
        } else {
            array_push($errors,Arikaim::errors()->getError("PHP_VERSION_ERROR"));
        }
        array_push($info['items'],$item);

        // PDO extension
        $item['message'] = 'PDO php extension';     
        $item['status'] = (System::hasPhpExtension('PDO') == true) ? 1 : 0;
        array_push($info['items'],$item);

        // PDO driver
        $pdoDriver = Arikaim::config()->getByPath('db/driver');
       
        $item['message'] = "$pdoDriver PDO driver";
        $item['status'] = 0; // error
        if (System::hasPdoDriver($pdoDriver) == true) {
            $item['status'] = 1; // ok
        } else {
            array_push($errors,Arikaim::errors()->getError("PDO_ERROR"));         
        }
        array_push($info['items'],$item);

        // curl extension
        $item['message'] = 'Curl PHP extension';
        $item['status'] = (System::hasPhpExtension('curl') == true) ? 1 : 2;
           
        array_push($info['items'],$item);

        // zip extension
        $item['message'] = 'Zip PHP extension';    
        $item['status'] = (System::hasPhpExtension('zip') == true) ? 1 : 2;

        array_push($info['items'],$item);
        
        // GD extension 
        $item['message'] = 'GD PHP extension';      
        $item['status'] = (System::hasPhpExtension('gd') == true) ? 1 : 2;
          
        array_push($info['items'],$item);
        $info['errors'] = $errors;
        
        return $info;
    }  

    /**
     * Return core migration classes
     *
     * @return array
     */
    private function getSystemSchemaClasses()
    {
        return [
            'UsersSchema',
            'PermissionsSchema',
            'PermissionRelationsSchema',
            'UserGroupsSchema',
            'UserGroupMembersSchema',
            'EventsSchema',
            'EventSubscribersSchema',
            'ExtensionsSchema',
            'ModulesSchema',
            'JobsSchema',
            'LanguageSchema',
            'OptionsSchema',
            'PermissionsSchema',
            'RoutesSchema',
            'AccessTokensSchema',
            'DriversSchema'
        ];
    }

    /**
     * Get core db table names
     *
     * @return array
     */
    private static function getSystemDbTableNames()
    {
        return [
            'options',         
            'extensions',
            'modules',
            'permissions',
            'permission_relations',
            'users',
            'user_groups',
            'user_group_members',
            'routes',
            'event_subscribers',
            'events',
            'language',
            'jobs',
            'access_tokens',
            'drivers'
        ];
    }
}
