<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core;

use Arikaim\Core\Framework\Router\ArikaimRouter;
use Arikaim\Core\App\AppContainer;
use Arikaim\Core\Http\Session;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Framework\Application;
use ErrorException;

/**
 * Arikaim core class
 */
class Arikaim  
{
    /**
     * Url scheme
     *
     * @var string|null
     */
    protected static $scheme;

    /**
     * Application object
     * 
     * @var object
    */
    public static $app;
    
    /**
     * Get container service
     *
     * @param string $name Service name
     * @param array $arguments Service params
     * @return mixed service|null
    */
    public static function __callStatic($name, $arguments)
    {    
        return Self::get($name);
    }
    
    /**
     * Get item from app container 
     *
     * @param string $name
     * @return mixed
     */
    public static function get(string $name)
    {
        return $GLOBALS['container']->get($name);
    }

    /**
     * Get item from service container
     *
     * @param string $name
     * @return mixed
     */
    public static function getService(string $name)
    {
        return $GLOBALS['container']->get('service')->get($name);
    }

    /**
     * Check item exists in container
     *
     * @param string $name Item name.
     * @return boolean
    */
    public static function has(string $name): bool
    {
        return $GLOBALS['container']->has($name);
    }

    /**
     * Return service container object.
     *
     * @return ContainerInterface
    */
    public static function getContainer()
    {
        return $GLOBALS['container'];
    }

    /**
    * System init
    *
    * @param bool $showErrors
    * @param bool $console
    * @param array|null $config
    * @return void
    */
    public static function systemInit(bool $showErrors = false, bool $console = false, ?array $config = null): void
    {
        // scheme
        Self::$scheme = (
            (isset($_SERVER['HTTPS']) == true && $_SERVER['HTTPS'] !== 'off') || 
            (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') || 
            ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') == true)
        ) ? 'https' : 'http';

        \ini_set('display_errors',(int)$showErrors);
        \ini_set('display_startup_errors',(int)$showErrors);
        \error_reporting(($showErrors == true) ? E_ALL : 0); 
    
        // Init constants     
        (\defined('ROOT_PATH') == false) ? \define('ROOT_PATH',Self::getRootPath($console)) : null;
        \define('DOMAIN',Self::$scheme . '://' . ($config['environment']['host'] ?? Self::resolveHost($_SERVER)));  
        \define('BASE_PATH',$config['environment']['basePath'] ?? Self::resolveBasePath($_SERVER,DOMAIN) );      
        \define('APP_PATH',ROOT_PATH . BASE_PATH . DIRECTORY_SEPARATOR . 'arikaim');       
        \define('APP_URL',DOMAIN . BASE_PATH . '/arikaim');
        \define('CORE_NAMESPACE','Arikaim\\Core');     

        $loader = new \Arikaim\Core\System\ClassLoader(BASE_PATH,ROOT_PATH,'Arikaim\Core',[
            'Arikaim\Extensions',
            'Arikaim\Modules'
        ]);
        $loader->register();
                
        // load config
        $config = $config ?? include (Path::CONFIG_PATH . 'config.php');
        // Datetime zone       
        \date_default_timezone_set($config['settings']['timeZone'] ?? \date_default_timezone_get());
        // Define date, time, number constants  
        \define('CURRENT_NUMBER_FORMAT',$config['settings']['numberFormat'] ?? null);                             
        \define('CURRENT_DATE_FORMAT',$config['settings']['dateFormat'] ?? null);           
        \define('CURRENT_TIME_FORMAT',$config['settings']['timeFormat'] ?? null);  

        // Create app
        $GLOBALS['container'] = AppContainer::create($console,$config);
        $GLOBALS['container']->add('class.loader',$loader);        
        $GLOBALS['arikaim'] = &$GLOBALS['container'];
        
        // add headers from config file
        foreach($config['headers'] ?? [] as $header) {            
            \header($header);
        }      

        // create app
        Self::$app = new Application(
            $GLOBALS['arikaim'],
            new ArikaimRouter($GLOBALS['arikaim'])
        );
    }

    /**
     * Create Arikaim system. Create container services, load system routes 
     *     
     * @param boolean $showErrors   
     * @param array|null $config
     * @return void
    */
    public static function init(bool $showErrors = false, ?array $config = null): void 
    {        
        Self::systemInit($showErrors,false,$config);
        
        \set_error_handler(function($num, $message, $file, $line) {
            throw new ErrorException($message,0,$num,$file,$line);
        });

        // Session init
        Session::start();                

        // add global middlewares
        $middlewares = $config['middleware'] ?? Self::config()->get('middleware',[]);   
        Self::$app->setMiddlewares($middlewares);

        // boot db
        Self::get('db');
    }
    
    /**
     * Create response object
     *
     * @return ResponseInterface
     */
    public static function response()
    {
        return Self::$app->getFactory()->createResponse();
    }

    /**
     * Start Arikaim
     * 
     * @param bool $showErrors
     * @param array|null $config
     * @return void
    */
    public static function run(bool $showErrors = false, ?array $config = null): void 
    {      
        Self::init($showErrors,$config);    
        Self::$app->run(null,$config['settings']);                     
    }
    
    /**
     * Return error message
     *
     * @param string $errorCode Error code
     * @param array $params Erorr params
     * @param string|null $default
     * @return string
    */
    public static function getError(string $errorCode, array $params = [], ?string $default = 'UNKNOWN_ERROR'): string 
    {
        return Self::errors()->getError($errorCode,$params,$default);
    }

    /**
     * Return console root path.
     *
     * @return string
    */
    public static function getConsoleRootPath(): string
    {
        return \constant('ROOT_PATH') ?? $_SERVER['PWD'];
    }

    /**
     * Return root path.
     *
     * @param bool $console
     * @return string
    */
    public static function getRootPath(bool $console): string 
    {      
        // get root path for console run
        return ($console == false) ? \rtrim(\realpath($_SERVER['DOCUMENT_ROOT']),DIRECTORY_SEPARATOR) : Self::getConsoleRootPath();
    }

    /**
     * Return base path.
     *
     * @return string
    */
    public static function getBasePath(): string 
    {        
        return \constant('BASE_PATH') ?? '';      
    }

    /**
     * Return domain url.
     *
     * @return string
    */
    public static function getDomain(): string 
    {      
        return \constant('DOMAIN') ?? (Self::$scheme . '://' . Self::resolveHost($_SERVER));
    }

    /**
     * Get host
     *
     * @return string
     */
    public static function getHost(): string 
    {      
        return \parse_url(DOMAIN,PHP_URL_HOST);
    }

    /**
     * Get port
     *
     * @param string $prefix
     * @return string
     */
    public static function getPort(string $prefix = ':'): string
    {
        return (($_SERVER['SERVER_PORT'] ?? 80) == 80) ? '' : $prefix . $_SERVER['SERVER_PORT'];
    }

    /**
     * Return true if script is run from console.
     *
     * @return boolean
    */
    public static function isConsole(): bool
    {
        return (\php_sapi_name() == 'cli');      
    }   
    
    /**
     * Resolve site host
     *
     * @param array $env
     * @return string
     */
    public static function resolveHost(array $env): string
    {
        // host
        if (empty($env['HTTP_HOST']) == false) {
            return $env['HTTP_HOST'];
        }         

        $host = $env['SERVER_NAME'] ?? '';    
        if (\preg_match('/^(\[[a-fA-F0-9:.]+\])(:\d+)?\z/',$host,$matches) == false) {           
            return (\strpos($host,':') !== false) ? \strstr($host,':', true) : $host;                             
        }           
       
        return $host;
    }

    /**
     * Resolve site host
     *
     * @param array $env
     * @return string
     */
    public static function resolveBasePath(array $env, string $host): string
    {
        // path
        $scriptName = (string)\parse_url($env['SCRIPT_NAME'],PHP_URL_PATH);
        $scriptDir = \dirname($scriptName);      
        $uri = $env['REQUEST_URI'] ?? '';  
        $uri = (string)\parse_url($host . $uri,PHP_URL_PATH);
         
        // base path
        if (\stripos($uri,$scriptName) === 0) {
            $basePath = $scriptName;
        } elseif ($scriptDir !== '/' && \stripos($uri,$scriptDir) === 0) {
            $basePath = $scriptDir;
        } 
        $basePath = \rtrim(\str_ireplace('index.php','',$basePath ?? ''),DIRECTORY_SEPARATOR);
        
        return ($basePath == '/') ? '' : $basePath;        
    }   
}
