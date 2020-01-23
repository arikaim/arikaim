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

use Arikaim\Container\Container;
use Arikaim\Core\Events\EventsManager;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\View\Template\Extension;
use Arikaim\Core\App\TwigExtension;
use Arikaim\Core\Packages\PackageManagerFactory;
use Arikaim\Core\Packages\PackageFactory;
use Arikaim\Core\Routes\Routes;
use Arikaim\Core\App\Install;
use PDOException;

/**
 * Create system services
 */
class ServiceContainer
{
    /**
     * Init default services
     *
     * @return Container
     */
    public static function init($container)
    {
        // Cache 
        $container['cache'] = function($container) {            
           // $enabled = $container->get('settings')->get('cache',false);   
            $routeCacheFile = Path::CACHE_PATH . "/routes.cache.php";            
            return new \Arikaim\Core\Cache\Cache(Path::CACHE_PATH,$routeCacheFile,null,true);
        };
        // Config
        $container['config'] = function($container) {    
            $cache = $container->get('cache');                         
            $config = new \Arikaim\Core\System\Config("config.php",$cache,Path::CONFIG_PATH);         
            return $config;
        }; 

        // init cache status
        $container->get('cache')->setStatus($container->get('config')['settings']['cache']);

        // Events manager 
        $container['event'] = function() {
            return new EventsManager(Model::Events(),Model::EventSubscribers());
        };
        // Storage
        $container['storage'] = function($container) {
            return new \Arikaim\Core\Storage\Storage($container['event']);
        };
        // Http client  
        $container['http'] = function() {
            return new \Arikaim\Core\Http\HttpClient();
        }; 
        // Package manager factory
        $container['packages'] = function ($container) {     
            return new PackageManagerFactory($container['cache'],$container['storage'],$container['http']);          
        };
        // Init template view. 
        $container['view'] = function ($container) {                        
            $cache = (isset($container->get('config')['settings']['cache']) == true) ? Path::VIEW_CACHE_PATH : false;
            $debug = (isset($container->get('config')['settings']['debug']) == true) ? $container->get('config')['settings']['debug'] : true;
             
            return new \Arikaim\Core\View\View(
                $container['cache'],
                Path::VIEW_PATH,
                Path::EXTENSIONS_PATH, 
                Path::TEMPLATES_PATH,
                Path::COMPONENTS_PATH,
                ['cache' => $cache,'debug' => $debug,'autoescape' => false]
            );           
        };    
        // Init page components.
        $container['page'] = function($container) {    
            $packageFactory = new PackageFactory();
            return new \Arikaim\Core\View\Html\Page($container->get('view'),$packageFactory);
        }; 
        // Errors  
        $container['errors'] = function($container) {
            $systemErrors = $container->get('config')->loadJsonConfigFile('errors.json');       
            return new \Arikaim\Core\System\Error\Errors($container['page'],$systemErrors);          
        };
        // Access
        $container['access'] = function($container) {
            $user = Model::Users();  
            $permissins = Model::PermissionRelations();    
            $access = new \Arikaim\Core\Access\Access($permissins);

            return new \Arikaim\Core\Access\Authenticate($user,$access,$container['errors']);
        };
        // Init Eloquent ORM
        $container['db'] = function($container) {  
            try {  
                $relations = $container->get('config')->load('relations.php');
                $db = new \Arikaim\Core\Db\Db($container->get('config')['db'],$relations);
            } catch(PDOException $e) {
                if (Install::isInstalled() == true) {
                    $container->get('errors')->addError('DB_CONNECTION_ERROR');
                }                
            }      
            return $db;
        };     
        // Routes
        $container['routes'] = function($container) { 
            return new Routes(Model::Routes(),$container['cache']);  
        };
        // Options
        $container['options'] = function($container) { 
            $options = Model::Options();  
            return new \Arikaim\Core\Options\Options($options,$container->get('cache'));          
        };
        // Mailer
        $container['mailer'] = function($container) {
            return new \Arikaim\Core\Mail\Mailer($container['options'],$container['page']);
        };
        // Drivers
        $container['driver'] = function() {   
            return new \Arikaim\Core\Driver\DriverManager(Model::Drivers());  
        };
        // Logger
        $container['logger'] = function($container) {                     
            $logger = new \Arikaim\Core\Logger\Logger(Path::LOGS_PATH);
            if ($container->get('options')->get('logger',true) == false) {
                $logger->disable();
            }
            return $logger;
        };      
        // Jobs queue
        $container['queue'] = function($container) {           
            return new \Arikaim\Core\Queue\QueueManager(Model::Jobs(),$container['event'],$container['options']);          
        };   
       
        // Add template extensions
        $extension = new Extension($container->get('cache'),BASE_PATH,Path::VIEW_PATH,$container->get('page'),$container->get('access'));
        $container->get('view')->addExtension($extension);

        $twigExtension = new TwigExtension($container->get('cache'),$container->get('access'),$container->get('options'));
        $container->get('view')->addExtension($twigExtension);

        return $container;
    }
}
