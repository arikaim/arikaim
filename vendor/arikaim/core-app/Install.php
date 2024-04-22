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

use Arikaim\Core\Interfaces\Access\AccessInterface;
use Arikaim\Core\Db\Schema;
use Arikaim\Core\Db\Model;
use Arikaim\Core\System\System;
use Arikaim\Core\System\Process;
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Path;
use Exception;
use Closure;

/**
 * Arikaim install
 */
class Install 
{
    /**
     * Set config files writable
     *
     * @return bool
     */
    public static function setConfigFilesWritable(): bool
    {
        global $arikaim;

        $result = true;
        // config file
        $configFile = $arikaim->get('config')->getConfigFile();
        $result = (File::setWritable($configFile) == false) ? false : $result;
        // relatins file 
        $configFile = PAth::CONFIG_PATH . 'relations.php';
        $result = (File::setWritable($configFile) == false) ? false : $result;

        // service container config
        $configFile = PAth::CONFIG_PATH . 'service-providers.php';
        if (File::exists($configFile) == true) {
            File::setWritable($configFile);
        }

        // store config
        $configFile = PAth::CONFIG_PATH . 'arikaim-store.php';
        if (File::exists($configFile) == true) {
            File::setWritable($configFile);
        }

        return $result;
    }

    /**
     * Prepare install
     *
     * @param Closure|null $onError
     * @param Closure|null $onProgress
     * @param array|null $requirements
     * @return bool
     */
    public function prepare(?Closure $onProgress = null, ?Closure $onError = null, ?array $requirements = null): bool
    {
        // chown www-data:www-data (recursive)    
        Process::runShellCommand('chown www-data:www-data ' . APP_PATH . ' -R ');

        $status = true;
        // check requirments
        $requirements = $requirements ?? Self::checkSystemRequirements();
        foreach ($requirements['errors'] as $error) {
            $this->callback($onError,$error);
        }
        if (\count($requirements['errors']) > 0) {
            $status = false;
        }

        // cache dir
        File::makeDir(Path::CACHE_PATH,0777);
        $result = File::isWritable(Path::CACHE_PATH); 
        if ($result == false) {
            $this->callback($onError,"Can't set cache dir writable.");
            $status = false;
        } else {
            $this->callback($onProgress,"Cache directory set writable.");
        }
        // set config files writable
        $result = Self::setConfigFilesWritable();
        if ($result == false) {
            $this->callback($onError,"Can't set config files writable.");
            $status = false;
        } else {
            $this->callback($onProgress,"Config files set writable.");
        }

        return $status;
    }

    /**
     * Call closure
     *
     * @param Closure|null $closure
     * @param string $message
     * @return void
     */
    protected function callback($closure, $message): void
    {
        if (\is_callable($closure) == true) {
            $closure($message);
        }
    }
    
    /**
     * Create db if not exist
     *
     * @param string $databaseName
     * @return boolean
     */
    public function createDb(string $databaseName): bool
    {
        global $arikaim;

        if ($arikaim->get('db')->has($databaseName) == false) {
            $charset = $arikaim->get('config')->getByPath('db/charset'); 
            $collation = $arikaim->get('config')->getByPath('db/collation');
            return $arikaim->get('db')->createDb($databaseName,$charset,$collation);                    
        }
        
        return true;
    }

    /**
     * Install Arikaim
     *
     * @param Closure|null $onProgress
     * @param Closure|null $onProgressError    
     * @param array|null $config
     * @return boolean
     */
    public function install(?Closure $onProgress = null, ?Closure $onProgressError = null, ?array $config = null): bool 
    {         
        global $arikaim;
        System::setTimeLimit(0);

        if (\is_array($config) == true) {
            $arikaim->get('config')->set('db',$config);
        } 
        // reboot connection    
        $arikaim->get('db')->reboot($arikaim->get('config')->get('db')); 

        // Create Arikaim DB tables
        $result = $this->createDbTables(function($class) use ($onProgress) {
            $this->callback($onProgress,'Db table model created ' . $class);
        },function($class) use ($onProgressError) {
            $this->callback($onProgressError,'Error creatinng db table model ' . $class);
        });      

        if ($result !== true) {    
            $this->callback($onProgressError,"Error creating system db tables.");        
            return false;
        } 
        $this->callback($onProgress,'System db tables created.'); 

        // Add control panel permisison item       
        $result = $arikaim->get('access')->addPermission(
            AccessInterface::CONTROL_PANEL,
            AccessInterface::CONTROL_PANEL,
            'Arikaim control panel access.'
        );
        if ($result == false) {    
            if (Model::Permissions()->has(AccessInterface::CONTROL_PANEL) == false) {             
                $this->callback($onProgressError,'REGISTER_PERMISSION_ERROR');
                return false;
            }           
        } else {
            $this->callback($onProgress,'Control panel permission added.');
        }

        // register core events
        $this->registerCoreEvents();
        $this->callback($onProgress,'Register system events');      

        // create admin user if not exists       
        $result = $this->createDefaultAdminUser();
        if ($result === false) {
            $this->callback($onProgressError,'Error creating control panel user.');
            return false;
        }
        $this->callback($onProgress,'Control panel user created.');      
       
        // add date, time, number format items     
        $this->initDefaultOptions();
        $this->callback($onProgress,'Default system options saved.');   

        // install drivers
        $result = $this->installDrivers();
        if ($result === false) {
            $this->callback($onProgressError,'Error register cache driver.');
        }

        // set storage folders              
        $this->initStorage();
        $this->callback($onProgress,'Storage folders created.'); 

        return true;
    } 

    /**
     * Install all modules
     *     
     * @param Closure|null $onProgress
     * @param Closure|null $onProgressError
     * @return boolean
     */
    public function installModules($onProgress = null, $onProgressError = null)
    {      
        global $arikaim;
        System::setTimeLimit(0);

        try {
            // Install modules
            $modulesManager = $arikaim->get('packages')->create('module');
            $result = $modulesManager->installAllPackages($onProgress,$onProgressError);
        } catch (Exception $e) {
            return false;
        }
     
        return $result;  
    }

    /**
     * Install all extensions packages
     *   
     * @param Closure|null $onProgress
     * @param Closure|null $onProgressError
     * @return boolean
     */
    public function installExtensions($onProgress = null, $onProgressError = null)
    {      
        global $arikaim;
        System::setTimeLimit(0);
        
        try {
            // Install extensions      
            $extensionManager = $arikaim->get('packages')->create('extension');
            $result = $extensionManager->installAllPackages($onProgress,$onProgressError);
        } catch (Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Chown command for storage fodler (recursive)
     *
     * @return void
     */
    public function changeStorageFolderOwner(): void
    {
        // chown www-data:www-data (recursive)    
        Process::runShellCommand('chown www-data:www-data ' . Path::STORAGE_PATH . ' -R ');
    }

    /**
     * Create storage folders
     *
     * @return boolean
     */
    public function initStorage(): bool
    {   
        global $arikaim;
        
        $this->changeStorageFolderOwner();

        if ($arikaim->get('storage')->has('bin') == false) {          
            $arikaim->get('storage')->createDir('bin');
        } 

        if ($arikaim->get('storage')->has('public') == false) {          
            $arikaim->get('storage')->createDir('public');
        } 

        if (File::exists(Path::STORAGE_TEMP_PATH) == false) {
            File::makeDir(Path::STORAGE_TEMP_PATH);
        }

        if (File::exists(Path::STORAGE_BACKUP_PATH) == false) {
            File::makeDir(Path::STORAGE_BACKUP_PATH);
        }

        if (File::exists(Path::STORAGE_REPOSITORY_PATH) == false) {
            File::makeDir(Path::STORAGE_REPOSITORY_PATH);
        }
        // set writable 
        File::setWritable(Path::STORAGE_TEMP_PATH);
        File::setWritable(Path::STORAGE_BACKUP_PATH);
        File::setWritable(Path::STORAGE_REPOSITORY_PATH);

        // delete symlink
        $linkPath = ROOT_PATH . BASE_PATH . DIRECTORY_SEPARATOR . 'public';
        $linkTarget = $arikaim->get('storage')->getFullPath('public') . DIRECTORY_SEPARATOR;
      
        if (@\is_link($linkPath) == false) {
            // create symlink 
            @\symlink($linkTarget,$linkPath); 
        }
      
        return true;     
    }

    /**
     * Register code events
     *
     * @return void
     */
    private function registerCoreEvents(): void
    {
        global $arikaim;

        // Routes
        $arikaim->get('event')->registerEvent('core.route.disable','After disable route.');
        $arikaim->get('event')->registerEvent('core.route.enable','After disable route.');
        $arikaim->get('event')->registerEvent('core.page.load','After html page is loaded.');
    } 

    /**
     * Create default control panel user
     *
     * @return boolean
     */
    private function createDefaultAdminUser(): bool
    {
        global $arikaim;

        $user = Model::Users()->getControlPanelUser();
        if ($user == false) {
            $user = Model::Users()->createUser('admin','admin');  
            if (empty($user->id) == true) {
                return false;
            }    
        }
    
        $result = Model::PermissionRelations()->setUserPermission(
            AccessInterface::CONTROL_PANEL,
            AccessInterface::FULL,
            $user->id
        );

        return (\is_object($result) == true);
    }

    /**
     * Set default options
     *
     * @return void
     */
    private function initDefaultOptions(): void
    {        
        global $arikaim;

        $arikaim->get('options')->setStorageAdapter(Model::Options());
        // mailer        
        $arikaim->get('options')->createOption('mailer.log',false,true);
        $arikaim->get('options')->createOption('mailer.log.error',false,true);
        $arikaim->get('options')->createOption('mailer.from.email','',true);
        $arikaim->get('options')->createOption('mailer.from.name','',true);      
        // admin
        $arikaim->get('options')->createOption('admin.menu.button','',false);                
    }

    /**
     * Install drivers
     *
     * @return bool
     */
    public function installDrivers(): bool
    {
        global $arikaim;

        // cache
        return $arikaim->get('driver')->install(
            'filesystem',
            'Doctrine\\Common\\Cache\\FilesystemCache',
            'cache',
            'Filesystem cache',
            'Filesystem cache driver',
            '1.8.0',
            []
        );
    }

    /**
     * Create core db tables
     *
     * @param Closure|null $onProgress
     * @param Closure|null $onError
     * @param bool $stopOnError
     * @return bool
     */
    public function createDbTables(?Closure $onProgress = null, ?Closure $onError = null, bool $stopOnError = true): bool
    {                         
        $classes = $this->getSystemSchemaClasses();
        $result = true;
        try {
            foreach ($classes as $class) {     
                $installed = Schema::install($class);                  
                if ($installed === false) {                                            
                    $this->callback($onError,$class);
                    if ($stopOnError == true) {
                        return false;
                    }
                    $result = false;       
                } else {
                    $this->callback($onProgress,$class);   
                }
            }      
        } catch (Exception $e) {
            $this->callback($onError,$e->getMessage());
            if ($stopOnError == true) {
                return false;
            }
            $result = false;    
        }
      
        return $result;
    }

    /**
     * Set system tables rows format to dynamic
     *
     * @return bool
     */
    public function systemTablesRowFormat(): bool
    {
        global $arikaim;

        $classes = $this->getSystemSchemaClasses();
       
        foreach ($classes as $class) { 
            $tableName = Schema::getTable($class);
            if ($tableName !== true) {
                $format = $arikaim->get('db')->getRowFormat($tableName);
                if (\strtolower($format) != 'dynamic') {
                    Schema::setRowFormat($tableName);
                }            
            }
        }
        
        return true;
    }

    /**
     * Check if system is installed.
     *
     * @return boolean
     */
    public static function isInstalled(): bool 
    {        
        global $arikaim;

        $errors = 0;            
        try {
            $arikaim->get('db')->initSchemaConnection(); 
            
            // check db
            $errors += $arikaim->get('db')->has($arikaim->get('config')->getByPath('db/database')) ? 0 : 1;
            if ($errors > 0) {
                return false;
            }

            // check db tables
            $tables = Self::getSystemDbTableNames();
            foreach ($tables as $tableName) {
                $errors += Schema::hasTable($tableName) ? 0 : 1;
            }
                    
            $result = Model::Users()->hasControlPanelUser();                          
            if ($result == false) {
                $errors++;
            }          

        } catch(Exception $e) {
            $errors++;
        }

        return ($errors == 0);   
    }

    /**
     * Verify system requirements
     * status   1 - ok, 2 - warning, 0 - error
     * 
     * @return array
     */
    public static function checkSystemRequirements(): array
    {
        global $arikaim;
        
        $info['items'] = [];
        $info['errors']['messages'] = '';
        $errors = [];

        // php 5.6 or above
        $phpVersion = System::getPhpVersion();
        $item['message'] = 'PHP ' . $phpVersion;
        $item['status'] = 0; // error   
        if (\version_compare($phpVersion,'7.4','>=') == true) {               
            $item['status'] = 1; // ok                    
        } else {
           $errors[] = 'PHP_VERSION_ERROR';
        }
        $info['items'][] = $item;

        // PDO extension
        $item['message'] = 'PDO php extension';     
        $item['status'] = (System::hasPhpExtension('PDO') == true) ? 1 : 0;
        $info['items'][] = $item;

        // PDO driver
        $pdoDriver = $arikaim->get('config')->getByPath('db/driver');
       
        $item['message'] = $pdoDriver . 'PDO driver';
        $item['status'] = 0; // error
        if (System::hasPdoDriver($pdoDriver) == true) {
            $item['status'] = 1; // ok
        } else {
           $errors[] = 'PDO_ERROR';         
        }
        $info['items'][] = $item;

        // curl extension
        $item['message'] = 'Curl PHP extension';
        $item['status'] = (System::hasPhpExtension('curl') == true) ? 1 : 2;
        $info['items'][] = $item;

        // zip extension
        $item['message'] = 'Zip PHP extension';    
        $item['status'] = (System::hasPhpExtension('zip') == true) ? 1 : 2;
        $info['items'][] = $item;
        
        // GD extension 
        $item['message'] = 'GD PHP extension';      
        $item['status'] = (System::hasPhpExtension('gd') == true) ? 1 : 2;
        $info['items'][] = $item;

        // fileinfo php extension
        $item['message'] = 'fileinfo PHP extension';      
        $item['status'] = (System::hasPhpExtension('fileinfo') == true) ? 1 : 2;
        $info['items'][] = $item;

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
            'RoutesSchema',
            'OptionsSchema',          
            'UsersSchema',
            'PermissionsSchema',
            'PermissionRelationsSchema',
            'UserGroupsSchema',
            'UserGroupMembersSchema',
            'EventsSchema',
            'EventSubscribersSchema',
            'ExtensionsSchema',
            'ModulesSchema',
            'QueueSchema',
            'JobsRegistrySchema',
            'LanguageSchema',
            'PermissionsSchema',
            'AccessTokensSchema',
            'DriversSchema',
            'LogsSchema'
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
            'queue',
            'jobs_registry',
            'access_tokens',
            'drivers'
        ];
    } 
}
