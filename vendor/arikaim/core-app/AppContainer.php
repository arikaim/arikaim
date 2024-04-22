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
use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Path;
use PDOException;

/**
 * Create system services
 */
class AppContainer
{
    /**
     * Init default services
     *
     * @param boolean $cosole
     * @param array $config
     * @return Container
     */
    public static function create(bool $console = false, $config = []): object
    {
        return new Container([
            'config' => function() {                            
                $config = new \Arikaim\Core\System\Config('config.php',Path::CONFIG_PATH);
                $config->setWriteProtectedVars([
                    'settings/jwtKey',
                    'settings/defaultLanguage',
                    'settings/disableInstallPage',
                    'settings/demoMode',
                    'db/username',
                    'db/password',
                    'db/driver',
                    'db/host',
                    'db/database'
                ]);         
                $config->setReadProtectedVars([
                    'settings/jwtKey',
                    'db/username',
                    'db/password'
                ]);  
    
                return $config;
            },
            'cache' => function() use($config) {                    
                return new \Arikaim\Core\Cache\Cache(
                    Path::CACHE_PATH,                  
                    $config['settings']['cacheDriver'] ?? \Arikaim\Core\Cache\Cache::VOID_DRIVER,
                    $config['settings']['cacheSaveTime'] ?? 7
                );
            },
            // Storage
            'storage' => function() {
                return new \Arikaim\Core\Storage\Storage();
            },
            // Http client  
            'http' => function() {
                return new \Arikaim\Core\Http\HttpClient();
            }, 
            // Package manager factory
            'packages' => function($container) {     
                return new \Arikaim\Core\Packages\PackageManagerFactory(
                    $container['cache'],
                    $container['storage'],
                    $container['http']
                );          
            },
            'access' => function() use($config) {
                return new \Arikaim\Core\Access\Access(
                    Model::PermissionRelations(),                
                    null,
                    ['key' => $config['settings']['jwtKey'] ?? 'jwtKey']
                );          
            },
            'view' => function ($container) use($config) {      
                $cacheStatus = $config['settings']['cache'] ?? false;                                            
               
                $view = new \Arikaim\Core\View\View(
                    $container['cache'],                   
                    Path::VIEW_PATH,
                    Path::EXTENSIONS_PATH, 
                    Path::TEMPLATES_PATH,
                    Path::COMPONENTS_PATH,
                    [
                        'access' => $container['access']
                    ],
                    [
                        'cache'      => ($cacheStatus == true) ? Path::VIEW_CACHE_PATH : false,
                        'debug'      => $config['settings']['debug'] ?? false,
                        'demo_mode'  => $config['settings']['demoMode'] ?? false,
                        'autoescape' => false
                    ],
                    $config['settings']['primaryTemplate'] ?? 'system',
                    $config['settings']['templateTheme'] ?? null
                );           
    
                // Add twig extension         
                $view->addExtension(new \Arikaim\Core\App\TwigExtension());
               
                return $view;
            },
            'page' => function($container) {                                
                return new \Arikaim\Core\View\Html\Page(
                    $container->get('view'),
                    $container['config']['settings']['defaultLanguage'] ?? 'en',
                    $container->get('config')->load('ui-library.php')
                );
            }, 
            // Errors  
            'errors' => function() use($console) {
                return new \Arikaim\Core\System\Error\Errors(
                    Path::CONFIG_PATH . 'errors.json',
                    Path::CONFIG_PATH . 'console-errors.json',
                    $console
                );          
            },
            // Init Eloquent ORM
            'db' => function() use($config) {  
                try {               
                    $relations = include (Path::CONFIG_PATH . 'relations.php');
                    $db = new \Arikaim\Core\Db\Db($config['db'],$relations);
                } catch(PDOException $e) {                            
                }      
                return $db;
            },
            // Routes storage 
            'routes.storage' => function() {            
                return Model::Routes();
            },
            // Routes
            'routes' => function($container) {            
                return new \Arikaim\Core\Routes\Routes(Model::Routes(),$container['cache']);  
            },
            // Options
            'options' => function($container) {                             
                return new \Arikaim\Core\Options\Options($container->get('cache'),Model::Options());               
            },            
            // Drivers
            'driver' => function() {   
                return new \Arikaim\Core\Driver\DriverManager(Model::Drivers());  
            },
            // Logger
            'logger' => function() use($config) {   
                return new \Arikaim\Core\Logger\Logger(
                    Path::LOGS_PATH . 'errors.log',
                    $config['settings']['logger'] ?? false,
                    $config['settings']['loggerHandler'] ?? 'file'
                );           
            },      
            // Init email view.
            'email' => function($container) use($config) { 
                return new \Arikaim\Core\View\Html\EmailView(
                    $container->get('view'),
                    $config['settings']['defaultLanguage'] ?? 'en'
                );
            },
            // Mailer
            'mailer' => function($container) use($config) {            
                $driverName =  $container['config']['settings']['mailerDriver'] ?? null;              
               
                return new \Arikaim\Core\Mail\Mailer([
                        'from_email' => $container['options']->getString('mailer.from.email',''),
                        'from_name'  => $container['options']->getString('mailer.from.name',''),
                        'log'        => $container['options']->get('mailer.log',false),
                        'log_error'  => $container['options']->get('mailer.log.error',false)               
                    ],
                    $container['email'],
                    (empty($driverName) == false) ? $container['driver']->create($driverName) : null,
                    $container['logger']
                );
            },    
            // Events manager 
            'event' => function($container) use($config) {
                return new \Arikaim\Core\Events\EventsManager(
                    Model::Events(),Model::EventSubscribers(),
                    $container['logger'],
                    [
                        'log' => $config['settings']['logEvents'] ?? false 
                    ]
                );
            },
            // Jobs queue
            'queue' => function($container) {                     
                return new \Arikaim\Core\Queue\QueueManager(Model::Queue(),$container['logger']);          
            },          
            // Modules manager
            'modules' => function($container) {           
                return new \Arikaim\Core\Extension\Modules($container->get('cache'));
            },         
            // Service manager
            'service' => function() {           
                return new \Arikaim\Core\Service\ServiceContainer();
            }, 
            // Content providers manager
            'content' => function() {           
                return new \Arikaim\Core\Content\ContentManager();
            }
        ]);     
    }
}
