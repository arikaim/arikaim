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

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

use Arikaim\Core\Interfaces\CacheInterface;
use Arikaim\Core\Interfaces\Access\AccessInterface;
use Arikaim\Core\Interfaces\OptionsInterface;
use Arikaim\Core\Utils\Mobile;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Db\Model;
use Arikaim\Core\View\Html\HtmlComponent;
use Arikaim\Core\Access\Csrf;
use Arikaim\Core\System\System;
use Arikaim\Core\System\Composer;
use Arikaim\Core\System\Update;
use Arikaim\Core\App\Install;
use Arikaim\Core\Arikaim;

/**
 *  Template engine functions, filters and tests.
 */
class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Model classes requires control panel access 
     *
     * @var array
     */
    protected $protectedModels = [
        'PermissionRelations',
        'Permissions',
        'Routes',
        'Modules',
        'Events',
        'Drivers',
        'Extensions',
        'Jobs',
        'EventSubscribers'
    ];

    /**
     * Protected services requires control panel access  
     *
     * @var array
     */
    protected $protectedServices = [
        'config',
        'packages'
    ];

    /**
     * Cache
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Access
     *
     * @var AccessInterface
     */
    protected $access;

    /**
     * options
     *
     * @var OptionsInterface
     */
    protected $options;

    /**
     * Constructor
     *
     * @param CacheInterface $cache
     * @param AccessInterface $access
     */
    public function __construct(CacheInterface $cache, AccessInterface $access, OptionsInterface $options)
    {
        $this->cache = $cache;
        $this->access = $access;
        $this->options = $options;
    }

    /**
     * Rempate engine global variables
     *
     * @return array
     */
    public function getGlobals() 
    {
        return [];
    }

    /**
     * Template engine functions
     *
     * @return array
     */
    public function getFunctions() 
    {
        $items = $this->cache->fetch('twig.functions');
        if (is_array($items) == true) {
            return $items;
        }

        $items = [
            new TwigFunction('isMobile',[$this,'isMobile']),
            // paginator
            new TwigFunction('paginate',['Arikaim\\Core\\Paginator\\SessionPaginator','create']),
            new TwigFunction('clearPaginator',['Arikaim\\Core\\Paginator\\SessionPaginator','clearPaginator']),            
            new TwigFunction('getPaginator',['Arikaim\\Core\\Paginator\\SessionPaginator','getPaginator']),
            new TwigFunction('getRowsPerPage',['Arikaim\\Core\\Paginator\\SessionPaginator','getRowsPerPage']),
            new TwigFunction('getViewType',['Arikaim\\Core\\Paginator\\SessionPaginator','getViewType']),
            // database            
            new TwigFunction('applySearch',['Arikaim\\Core\\Db\\Search','apply']),
            new TwigFunction('createSearch',['Arikaim\\Core\\Db\\Search','setSearchCondition']),
            new TwigFunction('searchValue',['Arikaim\\Core\\Db\\Search','getSearchValue']),
            new TwigFunction('getSearch',['Arikaim\\Core\\Db\\Search','getSearch']),
            new TwigFunction('getOrderBy',['Arikaim\\Core\\Db\\OrderBy','getOrderBy']),
            new TwigFunction('applyOrderBy',['Arikaim\\Core\\Db\\OrderBy','apply']),
            new TwigFunction('createModel',[$this,'createModel']),
            new TwigFunction('showSql',['Arikaim\\Core\\Db\\Model','getSql']),
            // other
            new TwigFunction('getConstant',["Arikaim\\Core\\Db\\Model",'getConstant']),
            new TwigFunction('hasExtension',[$this,'hasExtension']),
            new TwigFunction('getFileType',[$this,'getFileType']),         
            new TwigFunction('system',[$this,'system']),  
            new TwigFunction('getSystemRequirements',[$this,'getSystemRequirements']),                      
            new TwigFunction('package',[$this,'createPackageManager']),       
            new TwigFunction('service',[$this,'getService']),     
            new TwigFunction('installConfig',[$this,'getInstallConfig']),     
            new TwigFunction('access',[$this,'getAccess']),   
            new TwigFunction('getCurrentLanguage',[$this,'getCurrentLanguage']),
            new TwigFunction('getVersion',[$this,'getVersion']),
            new TwigFunction('getLastVersion',[$this,'getLastVersion']),
            
            new TwigFunction('getOption',[$this,'getOption']),
            new TwigFunction('getOptions',[$this,'getOptions']),
            new TwigFunction('csrfToken',[$this,'csrfToken']),                
            new TwigFunction('fetch',["Arikaim\\Core\\App\\Url",'fetch']),
            new TwigFunction('extractArray',[$this,'extractArray'],['needs_context' => true]),
           
            // date and time
            new TwigFunction('getTimeZonesList',["Arikaim\\Core\\Utils\\DateTime",'getTimeZonesList']),
            new TwigFunction('timeInterval',['Arikaim\\Core\\Utils\\TimeInterval','getInterval']),
            new TwigFunction('currentYear',[$this,'currentYear']),
            new TwigFunction('today',["Arikaim\\Core\\Utils\\DateTime",'getTimestamp']),
           
        ];
        $this->cache->save('twig.functions',$items,10);

        return $items;
    }

    /**
     * Template engine filters
     *
     * @return array
     */
    public function getFilters() 
    {       
        return [];
    }

    /**
     * Template engine tests
     *
     * @return array
     */
    public function getTests() 
    {
       return [];
    }

    /**
     * Template engine tags
     *
     * @return array
     */
    public function getTokenParsers()
    {
        return [];
    }

    /**
     * Gte accesss
     *
     * @return AccessInterface
     */
    public function getAccess()
    {
        return $this->access;
    } 

    /**
     * True if request is from mobile browser
     *
     * @return boolean
     */
    public function isMobile()
    {
        $mobile = new Mobile();

        return $mobile->isMobile();
    }

    /**
     * Get install config data
     *
     * @return array|false
     */
    public function getInstallConfig()
    {
        $daibled = Arikaim::config()->getByPath('settings/disableInstallPage');
       
        return ($daibled == true) ? false : Arikaim::get('config');         
    }

    /**
     * Get system requirements
     *
     * @return array
     */
    public function getSystemRequirements()
    {
        return Install::checkSystemRequirements();
    }

    /**
     * Get composer package current version
     *
     * @param string|null $packageName
     * @return string|false
     */
    public function getVersion($packageName = null)
    {
        $packageName = (empty($packageName) == true) ? Arikaim::getCorePackageName() : $packageName;       

        return Composer::getInstalledPackageVersion(ROOT_PATH . BASE_PATH,$packageName);     
    }

    /**
     * Get composer package last version
     *
     * @param  string|null $packageName
     * @return string|false
     */
    public function getLastVersion($packageName = null)
    {
        $packageName = (empty($packageName) == true) ? Arikaim::getCorePackageName() : $packageName;
        $update = new Update($packageName);
        
        return $update->getLastVersion();
    }

    /**
     * Get service from container
     *
     * @param string $name
     * @return mixed
     */
    public function getService($name)
    {
        if (\in_array($name,$this->protectedServices) == true) {
            return ($this->access->hasControlPanelAccess() == true) ? Arikaim::get($name) : false;           
        }

        return Arikaim::get($name);
    }

    /**
     * Create package manager
     *
     * @param string $packageType
     * @return PackageManagerInterface|null
     */
    public function createPackageManager($packageType)
    {
        // Control Panel only
        if ($this->access->hasControlPanelAccess() == false ) {
            return null;
        }
        
        return \Arikaim\Core\Arikaim::get('packages')->create($packageType);
    }

    /**
     * Create model 
     *
     * @param string $modelClass
     * @param string|null $extension
     * @return Model|false
     */
    public function createModel($modelClass, $extension = null)
    {
        if (\in_array($modelClass,$this->protectedModels) == true) {
            return ($this->access->hasControlPanelAccess() == true) ? Model::create($modelClass,$extension) : false;           
        }
     
        return Model::create($modelClass,$extension);
    }

    /**
     * Return true if extension exists
     *
     * @param string $extension
     * @return boolean
     */
    public function hasExtension($extension)
    {
        $model = Model::Extensions()->where('name','=',$extension)->first();  

        return is_object($model);          
    }

    /**
     * Return file type
     *
     * @param string $fileName
     * @return string
     */
    public function getFileType($fileName) 
    {
        return pathinfo($fileName,PATHINFO_EXTENSION);
    }

    /**
     * Return current year
     *
     * @return string
     */
    public function currentYear()
    {
        return date("Y");
    }
    
    /**
     * Return current language
     *
     * @return array|null
     */
    public function getCurrentLanguage() 
    {
        $language = HtmlComponent::getLanguage();
        $model = Model::Language()->where('code','=',$language)->first();

        return (is_object($model) == true) ? $model->toArray() : null;
    }

    /**
     * Get option
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = null) 
    {
        return $this->options->get($name,$default);          
    }

    /**
     * Get options
     *
     * @param string $searchKey
     * @return array
     */
    public function getOptions($searchKey)
    {
        return $this->options->searchOptions($searchKey);       
    }

    /**
     * Create obj
     *
     * @param string $class
     * @param string|null $extension
     * @return object|null
     */
    public function create($class, $extension = null)
    {
        if (class_exists($class) == false) {
            $class = (empty($extension) == false) ? Factory::getExtensionClassName($extension,$class) : Factory::getFullClassName($class);
        }
     
        return Factory::createInstance($class);            
    }
    
    /**
     * Return csrf token field html code
     *
     * @return string
     */
    public function csrfToken()
    {
        $token = Csrf::getToken(true);    

        return '<input type="hidden" name="csrf_token" value="'. $token . '">';
    }

    /**
     * Fetch url
     *
     * @param string $url
     * @return Response|null
     */
    public function fetch($url)
    {
        $response = \Arikaim\Core\Arikaim::get('http')->get($url);
        
        return (is_object($response) == true) ? $response->getBody() : null;
    }

    /**
     * Exctract array as local variables in template
     *
     * @param array $context
     * @param array $data
     * @return void
     */
    public function extractArray(&$context, $data) 
    {
        if (is_array($data) == false) {
            return;
        }
        foreach($data as $key => $value) {
            $context[$key] = $value;
        }
    }  

    /**
     * Get system info ( control panel access only )
     *
     * @return System
     */
    public function system()
    { 
        return ($this->access->hasControlPanelAccess() == true) ? new System() : null;
    }
}
