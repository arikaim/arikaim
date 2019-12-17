<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Template;

use ParsedownExtra;
use Twig\TwigFilter;
use Twig\TwigTest;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

use Arikaim\Core\Interfaces\CacheInterface;
use Arikaim\Core\Interfaces\Access\AccessInterface;
use Arikaim\Core\Interfaces\View\HtmlPageInterface;
use Arikaim\Core\View\Template\Tags\ComponentTagParser;
use Arikaim\Core\View\Template\Tags\MdTagParser;
use Arikaim\Core\View\Template\Template;
use Arikaim\Core\Http\Url;

/**
 *  Template engine functions, filters and tests.
 */
class Extension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Markdown parser
     *
     * @var object
     */
    protected $markdownParser;

    /**
     * Cache
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Permisisons manager
     *
     * @var AccessInterface
     */
    protected $access;

    /**
     * Extensions path
     *
     * @var string
     */
    protected $extensionsPath;

    /**
     * Constructor
     *
     * @param CacheInterface $cache
     * @param string $basePath
     * @param string $viewPath
     * @param string $libraryPath
     * @param string $extensionsPath
     * @param HtmlPageInterface $page
     * @param AccessInterface $access
     */
    public function __construct(CacheInterface $cache, $basePath, $viewPath, $libraryPath, $extensionsPath, HtmlPageInterface $page, AccessInterface $access)
    {
        $this->cache = $cache;
        $this->basePath = $basePath;
        $this->viewPath = $viewPath;
        $this->libraryPath = $libraryPath;
        $this->extensionsPath = $extensionsPath;
        $this->page = $page;
        $this->access = $access;
    }

    /**
     * Rempate engine global variables
     *
     * @return array
     */
    public function getGlobals() 
    {
        $templateName = Template::getTemplateName();
        $templateUrl = Url::getTemplateUrl($templateName);
        $systemTemplateUrl = Url::getTemplateUrl(Template::SYSTEM_TEMPLATE_NAME);
        
        return [
            'base_path'             => $this->basePath,
            'base_url'              => Url::BASE_URL,
            'template_url'          => $templateUrl,
            'current_template_name' => $templateName,
            'ui_path'               => $this->basePath . $this->viewPath,
            'system_template_url'   => $systemTemplateUrl,
            'system_template_name'  => Template::SYSTEM_TEMPLATE_NAME,
            'ui_library_path'       => $this->libraryPath,
            'ui_library_url'        => Url::LIBRARY_URL      
        ];
    }

    /**
     * Template engine functions
     *
     * @return array
     */
    public function getFunctions() 
    {
        $items = $this->cache->fetch('twig.component.functions');
        if (is_array($items) == true) {
            return $items;
        }
                     
        $items = [
            // html components
            new TwigFunction('component',[$this,'loadComponent'], ['needs_environment' => false,'is_safe' => ['html']]),
            new TwigFunction('componentProperties',[$this,'getProperties']),
            new TwigFunction('componentOptions',[$this,'getComponentOptions']),
            new TwigFunction('currentFramework',["Arikaim\\Core\\View\\Template\\Template",'getCurrentFramework']),
            // page
            new TwigFunction('getPageFiles',[$this,'getPageFiles']),
            new TwigFunction('getComponentsFiles',[$this,'getComponentsFiles']),          
            new TwigFunction('url',["Arikaim\\Core\\View\\Html\\Page",'getUrl']),        
            new TwigFunction('currentUrl',["Arikaim\\Core\\View\\Html\\Page",'getCurrentUrl']),
            // template
            new TwigFunction('getTemplateFiles',[$this,'getTemplateFiles']),
            new TwigFunction('getThemeFiles',[$this,'getThemeFiles']),
            new TwigFunction('getLibraryFiles',[$this,'getLibraryFiles']),
            new TwigFunction('getCurrentTheme',["Arikaim\\Core\\View\\Theme",'getCurrentTheme']),
            
            new TwigFunction('loadLibraryFile',[$this,'loadLibraryFile']),     
            new TwigFunction('loadComponentCssFile',[$this,'loadComponentCssFile']),  
            new TwigFunction('getLanguage',["Arikaim\\Core\\View\\Html\\Page","getLanguage"]),
            new TwigFunction('sessionInfo',["Arikaim\\Core\\Http\\Session","getParams"]),

            // macros
            new TwigFunction('macro',["Arikaim\\Core\\View\\Template\\Template","getMacroPath"]),         
            new TwigFunction('systemMacro',["Arikaim\\Core\\View\\Template\\Template","getSystemMacroPath"])
        ];
        $this->cache->save('twig.component.functions',$items,10);

        return $items;
    }

    /**
     * Template engine filters
     *
     * @return array
     */
    public function getFilters() 
    {       
        $items = $this->cache->fetch('twig.filters');
        if (is_array($items) == true) {
            return $items;
        }
        $items = [          
            // Html
            new TwigFilter('attr',["Arikaim\\Core\\View\\Template\\Filters",'attr'],['is_safe' => ['html']]),
            new TwigFilter('tag',["Arikaim\\Core\\Utils\\Html",'htmlTag'],['is_safe' => ['html']]),
            new TwigFilter('singleTag',["Arikaim\\Core\\Utils\\Html",'htmlSingleTag'],['is_safe' => ['html']]),
            new TwigFilter('startTag',["Arikaim\\Core\\Utils\\Html",'htmlStartTag'],['is_safe' => ['html']]),
            new TwigFilter('getAttr',["Arikaim\\Core\\Utils\\Html",'getAttributes'],['is_safe' => ['html']]),
            new TwigFilter('decode',["Arikaim\\Core\\Utils\\Html",'specialcharsDecode'],['is_safe' => ['html']]),
            // other
            new TwigFilter('ifthen',["Arikaim\\Core\\View\\Template\\Filters",'is']),
            new TwigFilter('dump',["Arikaim\\Core\\View\\Template\\Filters",'dump']),
            new TwigFilter('string',["Arikaim\\Core\\View\\Template\\Filters",'convertToString']),
            new TwigFilter('emptyLabel',["Arikaim\\Core\\View\\Template\\Filters",'emptyLabel']),
            new TwigFilter('sliceLabel',["Arikaim\\Core\\View\\Template\\Filters",'sliceLabel']),
            new TwigFilter('jsonDecode',["Arikaim\\Core\\Utils\\Utils",'jsonDecode']),
            new TwigFilter('baseClass',["Arikaim\\Core\\Utils\\Utils",'getBaseClassName']),            
            // date time
            new TwigFilter('dateFormat',["Arikaim\\Core\\Utils\\DateTime",'dateFormat']),
            new TwigFilter('dateTimeFormat',["Arikaim\\Core\\Utils\\DateTime",'dateTimeFormat']),
            new TwigFilter('timeFormat',["Arikaim\\Core\\Utils\\DateTime",'timeFormat']),
            // numbers
            new TwigFilter('numberFormat',["Arikaim\\Core\\Utils\\Number",'format']),
            // files
            new TwigFilter('fileSize',["Arikaim\\Core\\Utils\\File",'getSizeText']),
            // text
            new TwigFilter('renderText',["Arikaim\\Core\\Utils\\Text",'render']),
            new TwigFilter('sliceText',["Arikaim\\Core\\Utils\\Text",'sliceText']),
            new TwigFilter('titleCase',["Arikaim\\Core\\Utils\\Text",'convertToTitleCase']),
            new TwigFilter('md',[$this,'parseMarkdown']),
        ];

        $this->cache->save('twig.filters',$items,10);

        return $items;
    }

    /**
     * Template engine tests
     *
     * @return array
     */
    public function getTests() 
    {
        $items = $this->cache->fetch('twig.tests');
        if (is_array($items) == true) {
            return $items;
        }
        $items = [
            new TwigTest('haveSubItems',["Arikaim\\Core\\Utils\\Arrays",'haveSubItems']),
            new TwigTest('object',["Arikaim\\Core\\View\\Template\\Tests",'isObject']),
            new TwigTest('string',["Arikaim\\Core\\View\\Template\\Tests",'isString']),
            new TwigTest('access',[$this,'hasAccess']),
            new TwigTest('versionCompare',["Arikaim\\Core\\View\\Template\\Tests",'versionCompare'])
        ];
        $this->cache->save('twig.tests',$items,10);

        return $items;
    }

    /**
     * Template engine tags
     *
     * @return array
     */
    public function getTokenParsers()
    {
        return [
            new ComponentTagParser(),
            new MdTagParser()
        ];
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

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $params
     * @return void
     */
    public function loadComponent($name, $params = [])
    {
        return $this->page->createHtmlComponent($name,$params)->load();
    }

    /**
     * Get page fles
     *
     * @return array
     */
    public function getPageFiles()
    {
        return $this->page->getPageFiles();        
    }

    public function getTemplateFiles()
    {
        return $this->page->getTemplateFiles();       
    }

    /**
     * Return theme files
     *
     * @return array
     */
    public function getThemeFiles()
    {      
        return $this->page->getThemeFiles();
    }

    /**
     * Return library files
     *
     * @return array
     */
    public function getLibraryFiles()
    {
        return $this->page->getLibraryFiles();
    }

    /**
     * Get page fles
     *
     * @return array
     */
    public function getComponentsFiles()
    {
        return $this->page->getComponentsFiles();        
    }

    public function getProperties($name, $language = null)
    {
        return $this->page->createHtmlComponent($name,null,$language)->getProperties();
    }

    /**
     * Get comonent options ( control panel access is required)
     *
     * @param string $name
     * @return array|null
     */
    public function getComponentOptions($name)
    {
        return ($this->access->hasControlPanelAccess() == true) ? $this->page->createHtmlComponent($name)->getOptions() : null;
    }

    /**
     * Load component css file
     *
     * @param string $componentName
     * @return string
     */
    public function loadComponentCssFile($componentName)
    {
        $file = $this->page->getComponentFiles($componentName,'css');
        $content = (empty($file[0]) == false) ? File::read($file[0]['full_path'] . $file[0]['file_name']) : '';
        
        return ($content == null) ? '' : $content;
    }

    /**
     * Load Ui library file
     *
     * @param string $library
     * @param string $fileName
     * @return string
     */
    public function loadLibraryFile($library, $fileName)
    {      
        $file = $this->viewPath . 'library' . DIRECTORY_SEPARATOR . $library . DIRECTORY_SEPARATOR . $fileName;
        $content = File::read($file);

        return ($content == null) ? '' : $content;
    }

    /**
     * Check access 
     *
     * @param string $name Permission name
     * @param string|array $type PermissionType (read,write,execute,delete)   
     * @param mixed $authId 
     * @return boolean
     */
    public function hasAccess($name, $type = null, $authId = null)
    {
        return $this->access->hasAccess($name,$type);
    }

    /**
     * Return true if use ris logged 
     *
     * @return boolean
     */
    public function isLogged()
    {
        return $this->access->isLogged();
    }
}
