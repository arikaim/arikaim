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

use ParsedownExtra;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\TwigTest;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

use Arikaim\Core\View\Html\Page;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Http\Url;
use Arikaim\Core\Http\Session;
use Arikaim\Core\Routes\Route;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\View\Template\Tags\ComponentTagParser;
use Arikaim\Core\View\Template\Tags\MdTagParser;
use Arikaim\Core\View\Template\Tags\CacheTagParser;
use Arikaim\Core\View\Template\Tags\ErrorTagParser;
use Exception;

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
     * Protected services requires logged user
     *
     * @var array
     */
    protected $userProtectedServices = [      
        'storage'      
    ];

    /**
     * Markdown parser
     *
     * @var object
     */
    protected $markdownParser;

    /**
     * Rempate engine global variables
     *
     * @return array
     */
    public function getGlobals(): array 
    {
        return [
            'system_template_name'  => Page::SYSTEM_TEMPLATE_NAME,
            'domain'                => (\defined('DOMAIN') == true) ? DOMAIN : null,
            'base_url'              => Url::BASE_URL,     
            'base_path'             => BASE_PATH,     
            'templates_path'        => Path::TEMPLATES_PATH,   
            'DIRECTORY_SEPARATOR'   => DIRECTORY_SEPARATOR,        
            'ui_path'               => BASE_PATH . Path::VIEW_PATH,   
        ];
    }

    /**
     * Template engine functions
     *
     * @return array
     */
    public function getFunctions() 
    {
        return [
            // html components
            new TwigFunction('component',[$this,'loadComponent'],[                
                'needs_context'     => true,
                'is_safe'           => ['html']
            ]),                    
            // page              
            new TwigFunction('url',[Page::class,'getUrl']),   
            new TwigFunction('pageHead',[$this,'pageHead']),       
            new TwigFunction('addPageHeadCode',[$this,'addPageHeadCode']),       
            new TwigFunction('currentUrl',[$this,'getCurrentUrl']),        
            // template           
            new TwigFunction('loadLibraryFile',[$this,'loadLibraryFile']),    
            new TwigFunction('getLanguage',[$this,'getLanguage']),    
            new TwigFunction('getLanguages',[$this,'getLanguages']),    
            new TwigFunction('readThemeModules',[$this,'readThemeModules']),             
            // paginator
            new TwigFunction('paginate',['Arikaim\\Core\\Paginator\\SessionPaginator','create']),
            new TwigFunction('paginatorUrl',[$this,'getPaginatorUrl']),
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
            new TwigFunction('relationsMap',[$this,'getRelationsMap']),
            // other           
            new TwigFunction('getFileType',[$this,'getFileType']),           
            new TwigFunction('service',[$this,'getService']),    
            new TwigFunction('content',['Arikaim\\Core\\Arikaim','content']),     
            new TwigFunction('access',[$this,'getAccess']),   
            new TwigFunction('getCurrentLanguage',[$this,'getCurrentLanguage']),                          
            new TwigFunction('hasExtension',[$this,'hasExtension']),
            // session vars
            new TwigFunction('getSessionVar',[$this,'getSessionVar']),
            new TwigFunction('setSessionVar',[$this,'setSessionVar']),
            // 
            new TwigFunction('getOption',[$this,'getOption']),
            new TwigFunction('getOptions',[$this,'getOptions']),                 
            new TwigFunction('fetch',[$this,'fetch']),
            new TwigFunction('extractArray',[$this,'extractArray'],['needs_context' => true]),          
            // url
            new TwigFunction('getPageUrl',[$this,'getPageUrl']),         
            new TwigFunction('getTemplateUrl',['Arikaim\\Core\\Http\\Url','getTemplateUrl']),     
            new TwigFunction('getLibraryUrl',['Arikaim\\Core\\Http\\Url','getLibraryFileUrl']),  
            new TwigFunction('getExtensionViewUrl',['Arikaim\\Core\\Http\\Url','getExtensionViewUrl']),     
            // files
            new TwigFunction('getDirectoryFiles',[$this,'getDirectoryFiles']),
            new TwigFunction('isImage',['Arikaim\\Core\\Utils\\File','isImageMimeType']),
            // date and time
            new TwigFunction('getTimeZonesList',['Arikaim\\Core\\Utils\\DateTime','getTimeZonesList']),
            new TwigFunction('timeInterval',['Arikaim\\Core\\Utils\\TimeInterval','create']),          
            new TwigFunction('currentYear',['Arikaim\\Core\\Utils\\DateTime','getCurrentYear']),
            new TwigFunction('today',['Arikaim\\Core\\Utils\\DateTime','getCurrentTimestamp']),
            new TwigFunction('createDate',['Arikaim\\Core\\Utils\\DateTime','create']),
            // text
            new TwigFunction('Text',function($method,...$params) {
                return \Arikaim\Core\Utils\Text::$method(...$params);
            }),
            // unique Id
            new TwigFunction('createUuid',['Arikaim\\Core\\Utils\\Uuid','create']),
            new TwigFunction('createToken',['Arikaim\\Core\\Utils\\Utils','createToken']),
            // collections
            new TwigFunction('createCollection',['Arikaim\\Core\\Collection\\Collection','create']),
            new TwigFunction('createProperties',['Arikaim\\Core\\Collection\\PropertiesFactory','createFromArray']),
        ];    
    }

   /**
     * Read theme modules descriptor files
     *
     * @param string $templatePath
     * @param array|null  $modules
     * @return array
     */
    public function readThemeModules(string $templatePath, ?array $modules = null): array 
    {        
        if ($modules == null) {
            $json = \file_get_contents($templatePath . 'arikaim-package.json');
            $templateOptions = \json_decode($json,true);
            $modules = $templateOptions['modules'] ?? [];         
        }

        $data = [];
        foreach ($modules as $componentName) {
            $componentPath = 'components' . DIRECTORY_SEPARATOR . \str_replace('.',DIRECTORY_SEPARATOR,$componentName);
            try {
                $json = \file_get_contents($templatePath . $componentPath . DIRECTORY_SEPARATOR . 'arikaim-theme-module.json');
                $data[] = \json_decode($json,true);

            } catch (Exception $e) {  
                // not valid or missing theme module descriptor file       
            }
        }

        return $data;
    }

    /**
     *  Get access
     */
    public function getAccess()
    {
        global $arikaim;

        return $arikaim->get('access');
    }

    /**
     * Get paginator url
     *
     * @param string $pageUrl
     * @param integer $page
     * @param boolean $full
     * @param boolean $withLanguagePath
     * @return string
     */
    public function getPaginatorUrl($pageUrl, $page, $full = true, $withLanguagePath = false)
    {
        $path = (empty($pageUrl) == true) ? $page : $pageUrl . '/' . $page;
        
        return Page::getUrl($path,$full,$withLanguagePath);
    }

    /**
     * Return true if extension exists
     *
     * @param string|array $extension
     * @return boolean
     */
    public function hasExtension($extension): bool
    {
        $model = Model::Extensions();

        if (\is_array($extension) == true) {
            foreach ($extension as $item) {
                if (empty($item) == true) {
                    return false;
                }
                if ($model->where('name','=',$item)->first() == null) {
                    return false;
                }
            }
            return true;
        }

        return ($model->where('name','=',$extension)->first() != null);        
    }

    /**
     * Get cache
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        global $arikaim;

        return $arikaim->get('cache');
    } 

    /**
     * Get relatins type map (morph map)
     *
     * @return array|null
     */
    public function getRelationsMap(): ?array
    {
        global $arikaim;

        return $arikaim->get('db')->getRelationsMap();
    }

    /**
     * Return current url
     *
     * @param boolean $full
     * @param string|null $path
     * @return string
    */
    public function getCurrentUrl(bool $full = true, ?string $path = null): string
    {
        global $arikaim;

        $url = ($full == true) ? DOMAIN . $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'];  
        if ($path == null) {
            return $url;
        }
        
        $pagePath = $arikaim->get('view')->getGlobalVar('current_url_path','');

        return \rtrim($pagePath,'/') . $path;       
    }

    /**
     *  Get page head 
     * 
     *  @return object
     */
    public function pageHead(): object
    {
        global $arikaim;
        
        return $arikaim->get('page')->head();
    }

    /**
     *  Add page head code
     *  
     *  @param string $code
     *  @return void
     */
    public function addPageHeadCode(string $code): void
    {
        global $arikaim;

        $arikaim->get('page')->head()->set('code',$arikaim->get('page')->head()->get('code') . $code);
    }

    /**
     * Load component
     *
     * @param array $context
     * @param string $name
     * @param array|null $params
     * @param string|null $type
     * @return string|null
     */
    public function loadComponent($context, string $name, ?array $params = [], ?string $type = null)
    {              
        global $arikaim;
        
        return $arikaim
            ->get('page')
            ->renderHtmlComponent($name,$params ?? [],null,$type,[
                'component_location'      => $context['component_location'] ?? 0,
                'component_template_name' => $context['component_template_name'] ?? ''
            ])
            ->getHtmlCode();
    }

    /**
     * Get current page language
     *
     * @return string
     */
    public function getLanguage(): string
    {
        global $arikaim;

        return $arikaim->get('page')->getLanguage();
    }

    /**
     * Get theme languages
     *
     * @return array
     */
    public function getLanguages(): array
    {
        global $arikaim;

        return $arikaim->get('page')->getLanguages();
    }

    /**
     * Load Ui library file
     *
     * @param string $library
     * @param string $fileName
     * @return string
     */
    public function loadLibraryFile(string $library, string $fileName): string
    {      
        $file = Path::VIEW_PATH . 'library' . DIRECTORY_SEPARATOR . $library . DIRECTORY_SEPARATOR . $fileName;
       
        return (\file_exists($file) == false) ? '' : \file_get_contents($file);
    }

    /**
     * Get session var
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getSessionVar(string $name, $default = null)
    {
        return Session::get('vars.' . $name,$default);
    }

    /**
     * Set session var
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setSessionVar(string $name, $value): void
    {
        Session::set('vars.' . $name,$value);
    }

    /**
     * Get page url
     *
     * @param string $routeName
     * @param string|null $extension
     * @param array $params
     * @param boolean $relative
     * @param string|null $language
     * @return string|false
     */
    public function getPageUrl(
        string $routeName, 
        ?string $extension, 
        array $params = [], 
        bool $relative = false, 
        ?string $language = null
    )
    {
        global $arikaim;

        $route = $arikaim->get('routes')->getRoutes([
            'name'           => $routeName,
            'extension_name' => $extension
        ]);

        if (isset($route[0]) == false) {
            return false;
        }
        $urlPath = Route::getRouteUrl($route[0]['pattern'],$params);
        
        return Page::getUrl($urlPath,!$relative,$language);
    }

    /**
     * Template engine filters
     *
     * @return array
     */
    public function getFilters() 
    {       
        return [
            // Html
            new TwigFilter('attr',['Arikaim\\Core\\View\\Template\\Filters','attr'],['is_safe' => ['html']]),           
            new TwigFilter('getAttr',['Arikaim\\Core\\Utils\\Html','getAttributes'],['is_safe' => ['html']]),
            new TwigFilter('decode',['Arikaim\\Core\\Utils\\Html','specialcharsDecode'],['is_safe' => ['html']]),
            new TwigFilter('createHtmlId',['Arikaim\\Core\\Utils\\Html','createId'],['is_safe' => ['html']]),
            // other
            new TwigFilter('ifthen',['Arikaim\\Core\\View\\Template\\Filters','is']),
            new TwigFilter('dump',['Arikaim\\Core\\View\\Template\\Filters','dump']),
            new TwigFilter('string',['Arikaim\\Core\\View\\Template\\Filters','convertToString']),
            new TwigFilter('emptyLabel',['Arikaim\\Core\\View\\Template\\Filters','emptyLabel']),
            new TwigFilter('sliceLabel',['Arikaim\\Core\\View\\Template\\Filters','sliceLabel']),
            new TwigFilter('baseClass',['Arikaim\\Core\\Utils\\Utils','getBaseClassName']),                        
            // text
            new TwigFilter('renderText',['Arikaim\\Core\\Utils\\Text','render']),
            new TwigFilter('renderArray',['Arikaim\\Core\\Utils\\Text','renderMultiple']),
            new TwigFilter('sliceText',['Arikaim\\Core\\Utils\\Text','sliceText']),
            new TwigFilter('titleCase',['Arikaim\\Core\\Utils\\Text','convertToTitleCase']),
            new TwigFilter('md',[$this,'parseMarkdown']),

            new TwigFilter('jsonDecode',['Arikaim\\Core\\Utils\\Utils','jsonDecode']),
            // date time
            new TwigFilter('dateFormat',['Arikaim\\Core\\Utils\\DateTime','dateFormat']),
            new TwigFilter('dateTimeFormat',['Arikaim\\Core\\Utils\\DateTime','dateTimeFormat']),
            new TwigFilter('timeFormat',['Arikaim\\Core\\Utils\\DateTime','timeFormat']),
            new TwigFilter('convertDate',['Arikaim\\Core\\Utils\\DateTime','convert']),
            // numbers
            new TwigFilter('numberFormat',['Arikaim\\Core\\Utils\\Number','format']),
            new TwigFilter('toNumber',['Arikaim\\Core\\Utils\\Number','toNumber']),
            // text
            new TwigFilter('mask',['Arikaim\\Core\\Utils\\Text','mask']),
            new TwigFilter('pad',['Arikaim\\Core\\Utils\\Text','pad']),
            new TwigFilter('padLeft',['Arikaim\\Core\\Utils\\Text','padLeft']),
            new TwigFilter('padRight',['Arikaim\\Core\\Utils\\Text','padRight']),
            // files
            new TwigFilter('fileSize',['Arikaim\\Core\\Utils\\File','getSizeText']),
            new TwigFilter('baseName',['Arikaim\\Core\\Utils\\File','baseName']),
            new TwigFilter('relativePath',['Arikaim\\Core\\Utils\\Path','getRelativePath'])
        ];
    }

    /**
     * Template engine tests
     *
     * @return array
     */
    public function getTests() 
    {
        return [
            new TwigTest('haveSubItems',['Arikaim\\Core\\Utils\\Arrays','haveSubItems']),
            new TwigTest('object',['Arikaim\\Core\\View\\Template\\Tests','isObject']),
            new TwigTest('string',['Arikaim\\Core\\View\\Template\\Tests','isString']),
        ];
    }

    /**
     * Template engine tags
     *
     * @return array
     */
    public function getTokenParsers()
    {
        return [
            new ComponentTagParser(Self::class),
            new MdTagParser(Self::class),
            new CacheTagParser(Self::class),
            new ErrorTagParser()
        ];
    }   

    /**
     * Get service from container
     *
     * @param string $name
     * @return mixed
     */
    public function getService(string $name)
    {
        global $arikaim;

        if (\in_array($name,$this->protectedServices) == true) {
            return ($arikaim->get('access')->hasControlPanelAccess() == true) ? $arikaim->get($name) : false;           
        }

        if (\in_array($name,$this->userProtectedServices) == true) {
            return ($arikaim->get('access')->isLogged() == true) ? $arikaim->get($name) : false;           
        }

        if ($arikaim->has($name) == false) {
            // try from service container
            return $arikaim->get('service')->get($name);
        }

        return $arikaim->get($name);
    }

    /**
     * Get directory contents
     *
     * @param string $path
     * @param boolean $recursive
     * @param string|null $fileSystemName
     * @return array|false
     */
    public function getDirectoryFiles(string $path, bool $recursive = false, ?string $fileSystemName = null)
    {
        global $arikaim;
        
        // Control Panel only
        if ($arikaim->get('access')->isLogged() == false) {
            return false;
        }

        return $arikaim->get('storage')->listContents($path,$recursive,$fileSystemName);
    }

    /**
     * Create model 
     *
     * @param string $modelClass
     * @param string|null $extension
     * @param boolean $showError
     * @param boolean $checkTable
     * @return Model|null
     */
    public function createModel(?string $modelClass, ?string $extension = null, bool $showError = false): ?object
    {
        global $arikaim;

        if (\in_array($modelClass,$this->protectedModels) == true) {
            return ($arikaim->get('access')->hasControlPanelAccess() == true) ? Model::create($modelClass,$extension,null,$showError) : null;           
        }

        return Model::create($modelClass,$extension,null,$showError);
    }

    /**
     * Return file type
     *
     * @param string $fileName
     * @return string|null
     */
    public function getFileType(?string $fileName): ?string 
    {
        return (empty($fileName) == true) ? null : (string)\pathinfo($fileName,PATHINFO_EXTENSION);
    }

    /**
     * Return current language
     *
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        global $arikaim;

        return $arikaim->get('page')->getLanguage();
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
        global $arikaim;

        return $arikaim->get('options')->get($name,$default);          
    }

    /**
     * Get options
     *
     * @param string $searchKey
     * @param bool $compactKeys
     * @return array
     */
    public function getOptions($searchKey, $compactKeys = false)
    {
        global $arikaim;

        return $arikaim->get('options')->searchOptions($searchKey,$compactKeys);       
    }

    /**
     * Fetch url
     *
     * @param string $url
     * @return Response|null
     */
    public function fetch($url)
    {
        global $arikaim;

        $response = $arikaim->get('http')->get($url);
        
        return (\is_object($response) == true) ? $response->getBody() : null;
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
        if (\is_array($data) == false) {
            return;
        }
        foreach($data as $key => $value) {
            $context[$key] = $value;
        }
    }  

    /**
     * Parse Markdown
     *
     * @param array $context
     * @param string $content
     * @return string
     */
    public function parseMarkdown($content, $context = [])
    {
        if (empty($this->markdownParser) == true) {
            $this->markdownParser = new ParsedownExtra();
        }

        return $this->markdownParser->text($content);
    }
}
