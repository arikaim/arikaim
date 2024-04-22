<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
*/
namespace Arikaim\Core\View\Html;

use Arikaim\Core\View\Html\Component\BaseComponent;
use Arikaim\Core\View\Html\PageHead;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Http\Url;

use Arikaim\Core\View\Html\Component\Traits\IncludeOption;
use Arikaim\Core\View\Html\Component\Traits\Options;
use Arikaim\Core\View\Html\Component\Traits\Properties;
use Arikaim\Core\View\Html\Component\Traits\IndexPage;
use Arikaim\Core\View\Html\Component\Traits\UiLibrary;

use Arikaim\Core\Interfaces\View\ComponentInterface;
use Arikaim\Core\Interfaces\View\HtmlPageInterface;

/**
 * Html page
 */
class Page extends BaseComponent implements HtmlPageInterface
{    
    use 
        Options,
        Properties,   
        IndexPage,
        UiLibrary,
        IncludeOption;

    /**
     *  Control panel template name
     */
    const SYSTEM_TEMPLATE_NAME = 'system';
  
    /**
     *  Error page names
     */
    const PAGE_NOT_FOUND         = 'page-not-found';
    const SYSTEM_ERROR_PAGE      = 'system-error';
    const APPLICATION_ERROR_PAGE = 'application-error';

    /**
     * Default language
     *
     * @var string
     */
    private static $defaultLanguage;

    /**
     * Ui Library options
     *
     * @var array
     */
    protected $libraryOptions;

    /**
     * Component include files (js)
     *
     * @var array
    */
    protected $componentsFiles = [];

    /**
     * Component instances list
     *
     * @var array
     */
    protected $componentInstances = [];

    /**
     * View 
     *
     * @var Arikaim\Core\Interfaces\View\ViewInterface
     */
    protected $view;

    /**
     * Template url
     *
     * @var string
     */
    protected $templateUrl;

    /**
     * Page head properties
     *
     * @var PageHead|null
     */
    private $head;
    
    /**
     * Page languages
     *
     * @var array
     */
    protected $languages;

    /**
     * Constructor
     * 
     * @param Arikaim\Core\Interfaces\View\ViewInterface $view
     * @param string $defaultLanguage,
     * @param array $libraryOptions
     */
    public function __construct(object $view, string $defaultLanguage, array $libraryOptions = []) 
    {  
        parent::__construct(
            '',
            'pages',
            $defaultLanguage,
            $view->getViewPath(),
            $view->getExtensionsPath(),
            $view->getPrimaryTemplate(),
            ComponentInterface::PAGE_COMPONENT_TYPE
        );

        $this->view = $view; 
        $this->setOptionFile('page.json');

        $this->componentsFiles = [
            'js'  => [],
            'css' => []
        ];

        $this->libraryOptions = $libraryOptions;       
        $this->head = new PageHead();
        Self::$defaultLanguage = $defaultLanguage; 
        
        $this->languages = ['en'];
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages():array
    {
        return $this->languages;
    }
    
    /**
     * Return true if component is valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return $this->hasContent();
    }

    /**
     * Init component
     *
     * @return void
     */
    public function init(): void 
    {
        parent::init();
        
        $this->loadProperties();
        $this->loadOptions(false); 
        $this->addComponentFile('js');    
        $this->addComponentFile('css');           
        $this->hasHtmlContent = true;
        // options
        $this->processIncludeOption();      

        $this->templateUrl = Url::getTemplateUrl($this->getCurrentTemplate(),'/',false);
    }

    /**
     * Render html component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @param string|null $type
     * @param array $parent
     * 
     * @return \Arikaim\Core\Interfaces\View\HtmlComponentInterface
     */
    public function renderHtmlComponent(
        string $name, 
        array $params = [], 
        ?string $language = null, 
        ?string $type = null,
        array $parent = []
    )
    {
        $type = $type ?? ComponentInterface::ARIKAIM_COMPONENT_TYPE;
        $language = $language ?? $this->language;
        $params['template_path'] = Path::TEMPLATES_PATH . $this->getCurrentTemplate() . DIRECTORY_SEPARATOR;
        $params['template_url'] = $this->templateUrl;
        $params['current_language'] = $language;
        $params['page_component_name'] = $this->fullName;

        $component = $this->view->renderComponent($name,$language,$params,$type,$this->renderMode,$parent);
        $jsFiles = $component->getFiles('js');

        if (\count($jsFiles) > 0) {
            // include    
            if (isset($this->includedComponents[$name]) == false) {     
                $this->addIncludedComponent($name,$type,$component->id);       
                $this->componentsFiles['js'] = \array_merge($this->componentsFiles['js'],$jsFiles); 
                $this->includedComponents = \array_merge($this->includedComponents,$component->getIncludedComponents());             
            } else {
                $this->addComponentInstance($name,$type,$component->id);
            }
        } 
             
        return $component;   
    }

    /**
     * Add component instance
     *
     * @param string $name
     * @param string $type
     * @param string $id
     * @return void
     */
    public function addComponentInstance(string $name, string $type, string $id)
    {
        if (isset($this->componentInstances[$id]) == false) {      
            // incldue in component instances
            $item = [
                'name' => $name,
                'type' => $type,
                'id'   => $id
            ];

            $this->componentInstances[$id] = $item;
            // push to head code
            $this->head->addCommentCode('component instance'); 
            $this->head->addComponentInstanceCode($item);      
        }
    }

    /**
     * Get head properties
     *
     * @return PageHead
     */
    public function head()
    {
        return $this->head;
    }

     /**
     * Render component data
     *     
     * @param array $params   
     * @return bool
     */
    public function resolve(array $params = []): bool
    {        
        if ($this->isValid() == false) {           
            return false;                
        }
               
        $this->mergeContext($this->getProperties());
        $this->mergeContext($params);
        
        return true;
    }

    /**
     * Render page
     *
     * @param string $name
     * @param array $params
     * @param string|null $language      
     * @return ComponentInterface
    */
    public function render(string $name, array $params = [], ?string $language = null)
    {  
        $this->fullName = $name;       
        $this->language = $language ?? $this->language;

        $this->init();
        $this->resolve($params);  

        $includes = $this->getPageIncludes($name,$this->language);   
      
        $this->languages = $includes['languages'];

        $params['current_language'] = $this->language;
        $params['template_url'] = $this->templateUrl;
        $params['current_url_path'] = $params['current_path'] ?? '';
        $params['page_component_name'] = $name;
  
        // page head     
        $this->head->mergeItems($this->properties['head'] ?? [],false);
       
        $params = \array_merge_recursive($params,$this->properties); 
        // render page body code
        $params['body'] = $this->view->fetch($this->getTemplateFile(),$params);  

        // add page head code
        $this->addPageHeadCode($includes);
        
        $params['head'] = $this->head->toArray();               
        // fetch index file        
        $this->setHtmlCode($this->view->fetch($this->getIndexFile($this->templateName),$params));
      
        return $this;
    }

    /**
     * Push include code to page head
     *
     * @param array  $includes
     * @return void
     */
    protected function addPageHeadCode(array $includes): void
    {
        $this->head->addMetaTagCodeForItems([
            'title',
            'description',
            'keywords'
        ]);

        // add page head include html code
        $this->head->addCommentCode('library files');
        foreach($includes['library_files'] as $file) {
            $this->head->addLibraryIncludeCode($file);     
        }

        // template files
        $this->head->addCommentCode('template files');        
        foreach($includes['css'] as $file) {           
            $this->head->addLinkCode($file,'text/css','stylesheet');            
        }

        foreach($includes['js'] as $file) {  
            if (\is_array($file) == true) {
                $this->head->addComponentFileCode($file);                
            } else {
                $this->head->addScriptCode($file,'','');              
            }     
        }

        // component files
        $this->head->addCommentCode('component files'); 
        foreach(($this->componentsFiles['js'] ?? []) as $file) {   
            $this->head->addComponentFileCode($file);            
        }
        // page files
        $this->head->addCommentCode('page files'); 
        foreach($this->getFiles('js') as $file) { 
            $this->head->addComponentFileCode($file);                       
        }
    }

    /**
     * Get page includes
     *   
     * @param string $name  
     * @param string $language
     * @return array
     */
    protected function getPageIncludes(string $name, string $language): array
    {
        $includes = $this->view->getCache()->fetch('html.page.includes.' . $name . '.' . $language);
        if ($includes !== false) {             
            return $includes;
        }
        $includes = [];
        
        // page include files
        $pageIncludes = $this->getOption('include',[]);
        $templateOptions = $this->readTemplatePackageFile();
      
        $languages = $templateOptions['languages'] ?? ['en'];

        if ((bool)$this->getOption('remove-template-files',false) == false) {
            // merge template includes        
            $pageIncludes = \array_merge_recursive($templateOptions['include'] ?? [],$pageIncludes);
        }

        $pageIncludes = [
            'library'    => \array_unique($pageIncludes['library'] ?? []),
            'js'         => \array_unique($pageIncludes['js'] ?? []),
            'css'        => \array_unique($pageIncludes['css'] ?? []),
            'components' => \array_unique($pageIncludes['components'] ?? []),
            'languages'  => $languages
        ];
      
        // from page options
        $includes = $this->resolveIncludeFiles($pageIncludes,$this->templateUrl);

        // save to cache
        $this->view->getCache()->save('html.page.includes.' . $name . '.' . $language,$includes);

        return $includes;      
    }

    /**
     * Get components include files
     *
     * @return array
     */
    public function getComponentsFiles(): array
    {
        return $this->componentsFiles;
    }

    /**
     * Get components include files
     *
     * @return array
     */
    public function getComponentInstances(): array
    {
        return $this->componentInstances;
    }

    /**
     * Get current template name
     *
     * @return string
     */
    public function getCurrentTemplate(): string
    { 
        return (empty($this->templateName) == true) ? $this->primaryTemplate : $this->templateName;
    }

    /**
     * Return url link with current language code
     *
     * @param string|null $path
     * @param boolean $full
     * @param string|null $language
     * @return string
     */
    public static function getUrl($path = '', bool $full = false, ?string $language = null): string
    {       
        $path = $path ?? '';
        
        return Url::getUrl($path,!$full,$language,Self::$defaultLanguage);
    }

    /**
     * Get full page url
     *
     * @param string $path
     * @param string|null $language
     * @return string
     */
    public static function getFullUrl(string $path, ?string $language = null): string
    {
        return Self::getUrl($path,false,$language);
    }

    /**
     * Set current language
     *
     * @param string $language Language code
     * @return void
    */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * Read template packaeg file
     *
     * @return array
     */
    protected function readTemplatePackageFile(): array
    {
        $options = $this->view->getCache()->fetch('template.options.' . $this->templateName);
        if ($options !== false) {             
            return $options;
        }
       
        try {
            $json = \file_get_contents(Path::TEMPLATES_PATH . $this->templateName . DIRECTORY_SEPARATOR . 'arikaim-package.json');
            $options = \json_decode($json,true);
        } catch (\Exception $e) {
            return [];
        }
     
        $this->view->getCache()->save('template.options.' . $this->templateName,$options);

        return $options;
    }

    /**
     * Resolve include files
     *
     * @param array $include
     * @param string $url
     * @return array
     */
    protected function resolveIncludeFiles(array $include, string $url): array
    {                    
        // icnldue libraries
        $include['library_files'] = $this->getLibraryIncludeFiles($include['library'],$this->templateName . $this->id);

        // include js files
        $include['js'] = \array_map(function($file) use($url) {
            if (\filter_var($file,FILTER_VALIDATE_URL) !== false) {
                return $file;
            }            
            $tokens = \explode(':',$file);           
            if (isset($tokens[1]) == true) {
                $file = $tokens[1];
                $url = Url::getTemplateUrl($tokens[0],'/');
            } else {
                $file = $tokens[0];
            }
           
            return $url . 'js/' . $file; 
        },$include['js'] ?? []);

        // include css files
        $include['css'] = \array_map(function($file) use($url) {
            if (\filter_var($file,FILTER_VALIDATE_URL) !== false) {
                return $file;
            }
            $tokens = \explode(':',$file);           
            if (isset($tokens[1]) == true) {
                $file = $tokens[1];
                $url = Url::getTemplateUrl($tokens[0],'/');
            } else {
                $file = $tokens[0];
            }
            
            return $url . 'css/' . $file;         
        },$include['css'] ?? []);
       
        // include components
        foreach ($include['components'] as $componentName) {               
            $component = $this->view->createComponent($componentName,'en','empty');
            $include['js'][] = [
                'url'            => $component->getIncludeFile('js'),
                'component_name' => $component->getFullName(),
                'component_id'   => $component->id,
                'component_type' => 'js',      
            ];                    
        }    
              
        return $include;
    }

    /**
     * Get include library files
     *
     * @param array $libraryList
     * @param string $cacheKey
     * @return array
     */
    public function getLibraryIncludeFiles(array $libraryList, string $cacheKey): array
    {                
        $files = $this->view->getCache()->fetch('template.library.files.' . $cacheKey);        
        if ($files !== false) {            
            return $files;
        }
       
        $files = [];
        foreach ($libraryList as $library) {      
            list($libraryName,$libraryVersion,$libraryOption) = $this->parseLibraryName($library);
            $disabled = $this->libraryOptions[$libraryName]['disabled'] ?? false;
            if ($disabled == true) {
                continue;
            }
            
            $libraryFiles = $this->view->getCache()->fetch('library.files.' . $library . $libraryVersion ?? '');    
            if ($libraryFiles === false) {
                $libraryFiles = $this->getLibraryFiles($libraryName,$libraryVersion,$libraryOption);
                $this->view->getCache()->save('library.files.' . $library . $libraryVersion ?? '',$libraryFiles);  
            } 
        
            $files = \array_merge($files,$libraryFiles);       
        }
 
        // Save to cache
        $this->view->getCache()->save('template.library.files.' . $cacheKey,$files); 
                               
        return $files;
    }

    /**
     * Render page not found 
     *
     * @param array $data
     * @param string|null $language  
     * @param string|null $templateName        
     * @return ComponentInterface
    */
    public function renderPageNotFound(array $data = [], ?string $language = null, ?string $templateName = null)
    {
        $templateName = $templateName ?? $this->getCurrentTemplate();
        $templateName = ($templateName == Self::SYSTEM_TEMPLATE_NAME) ? $templateName . ':' : $templateName . '>';
        $language = $language ?? $this->language;

        return $this->render($templateName . Self::PAGE_NOT_FOUND,['error' => $data],$language);
    }

    /**
     * Render application error
     *
     * @param array $data
     * @param string|null $language    
     * @param string|null $templateName       
     * @return ComponentInterface
     */
    public function renderApplicationError(array $data = [], ?string $language = null, ?string $templateName = null)
    {
        $templateName = $templateName ?? $this->getCurrentTemplate();
        $templateName = ($templateName == Self::SYSTEM_TEMPLATE_NAME) ? $templateName . ':' : $templateName . '>';
        $language = $language ?? $this->language;

        return $this->render($templateName . Self::APPLICATION_ERROR_PAGE,['error' => $data],$language);
    }

    /**
     * Render system error(s)
     *
     * @param array $error
     * @param string|null $language   
     * @param string|null $templateName       
     * @return ComponentInterface
     */
    public function renderSystemError(array $error = [], ?string $language = null, ?string $templateName = null)
    {    
        $templateName = $templateName ?? $this->getCurrentTemplate();
        $templateName = ($templateName == Self::SYSTEM_TEMPLATE_NAME) ? $templateName . ':' : $templateName . '>';        
        $language = $language ?? $this->language;

        return $this->render($templateName . Self::SYSTEM_ERROR_PAGE,$error,$language);      
    }
}
