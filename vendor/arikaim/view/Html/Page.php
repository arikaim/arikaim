<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Html;

use Arikaim\Core\View\Html\ComponentData;
use Arikaim\Core\View\Html\Component;
use Arikaim\Core\View\Html\HtmlComponent;
use Arikaim\Core\View\Template\Template;
use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Collection\Arrays;
use Arikaim\Core\View\Html\PageHead;
use Arikaim\Core\View\Theme;
use Arikaim\Core\View\Html\ResourceLocator;
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Utils\Text;
use Arikaim\Core\Http\Session;
use Arikaim\Core\Http\Url;
use Arikaim\Core\View\Interfaces\ComponentDataInterface;
use Arikaim\Core\Interfaces\View\HtmlPageInterface;
use Arikaim\Core\Interfaces\View\ViewInterface;
use Arikaim\Core\Interfaces\Packages\PackageFactoryInterface;

/**
 * Html page
 */
class Page extends Component implements HtmlPageInterface
{   
    /**
     * Page head properties
     *
     * @var PageHead
     */
    protected $head;
    
    /**
     * Package factory
     *
     * @var PackageFactoryInterface
     */
    protected $packageFactroy;

    /**
     * Constructor
     * 
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view, PackageFactoryInterface $packageFactroy, $params = [], $language = null, $basePath = 'pages', $withOptions = true) 
    {  
        parent::__construct($view,null,$params,$language,$basePath,'page.json',$withOptions);

        $this->packageFactroy = $packageFactroy;       
        $this->head = new PageHead();
    }

    /**
     * Create html component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @param boolean $withOptions
     * @return HtmlComponent
     */
    public function createHtmlComponent($name, $params = [], $language = null, $withOptions = true)
    {
        return new HtmlComponent($this->view,$name,$params,$language,'components','component.json',$withOptions);      
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
     * Load page
     *
     * @param Response $response
     * @param string $name
     * @param array|object $params
     * @param string|null $language
     * @return Response|false
     */
    public function load($response, $name, $params = [], $language = null)
    {
        $html = $this->getHtmlCode($name,$params,$language);
        $response->getBody()->write($html);

        return $response;
    }

    /**
     * Get page html code
     *
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @return string
     */
    public function getHtmlCode($name, $params = [], $language = null)
    {
        if (empty($name) == true) {         
            return false;     
        }
        if (is_object($params) == true) {
            $params = $params->toArray();
        }
        $component = $this->render($name,$params,$language);
           
        return $component->getHtmlCode();
    }   

    /**
     * Render page
     *
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @return ComponentDataInterface
    */
    public function render($name, $params = [], $language = null)
    { 
        $this->setCurrent($name);
        $component = $this->createComponentData($name,$language);
        $params['component_url'] = $component->getUrl();
 
        $body = $this->getCode($component,$params);
        $indexPage = $this->getIndexFile($component);              
        $params = array_merge($params,['body' => $body, 'head' => $this->head->toArray()]);   
        $component->setHtmlCode($this->view->fetch($indexPage,$params));

        return $component;
    }

    /**
     * Get page index file
     *
     * @param object $component
     * @return string
     */
    private function getIndexFile($component)
    {
        $type = $component->getType();
        $fullPath = $component->getRootComponentPath() . $component->getBasePath() . DIRECTORY_SEPARATOR . "index.html";

        if (file_exists($fullPath) == true) {
            if ($type == ComponentData::EXTENSION_COMPONENT) {
                return $component->getTemplateName() . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $component->getBasePath() . DIRECTORY_SEPARATOR . "index.html"; 
            } 
            return $component->getTemplateName() . DIRECTORY_SEPARATOR . $component->getBasePath() . DIRECTORY_SEPARATOR . "index.html";            
        }
    
        // get from system template
        return Template::SYSTEM_TEMPLATE_NAME . DIRECTORY_SEPARATOR . $component->getBasePath() . DIRECTORY_SEPARATOR . "index.html";          
    }

    /**
     * Get page code
     *
     * @param ComponentDataInterface $component
     * @param array $params
     * @return string
     */
    public function getCode(ComponentDataInterface $component, $params = [])
    {     
        // include component files
        $this->view->properties()->merge('include.page.files',$component->getFiles());
        $this->includeFiles($component);

        $properties = $component->getProperties();
        
        if (isset($properties['head']) == true) {
            $head = Text::renderMultiple($properties['head'],$this->head->getParams()); 
            $this->head->replace($head); 
            if (isset($head['og']) == true) {
                $this->head->set('og',$head['og']);
                $this->head->resolveProperties('og');
            }
            if (isset($head['twitter']) == true) {
                $this->head->set('twitter',$head['twitter']);
                $this->head->resolveProperties('twitter');
            }         
        }
        $params = array_merge_recursive($params,(array)$properties);

        return $this->view->fetch($component->getTemplateFile(),$params);
    }
    
    /**
     * Return true if page exists
     *
     * @param string $pageName
     * @param string|null $language
     * @return boolean
     */
    public function has($pageName, $language = null) 
    {      
        $page = $this->createComponentData($pageName,$language);

        return $page->isValid();        
    }

    /**
     * Set page head properties
     *
     * @param Collection $head
     * @return void
     */
    public function setHead(Collection $head)
    {
        $this->head = $head;
    }

    /**
     * Get page fles
     *
     * @return array
     */
    public function getPageFiles()
    {
        return $this->view->properties()->get('include.page.files');        
    }

    /**
     * Get component files
     *
     * @return array
     */
    public function getComponentsFiles()
    {    
        return $this->view->properties()->get('include.components.files');
    }

    /**
     * Set curret page
     *
     * @param string $name
     * @return void
     */
    public function setCurrent($name)
    {   
        Session::set("page.name",$name);
    }

    /**
     * Get current page name
     *
     * @return string
     */
    public static function getCurrent()
    {
        return Session::get("page.name");
    }

    /**
     * Get language path
     *
     * @param string $path
     * @param string|null $language
     * @return string
     */
    public static function getLanguagePath($path, $language = null)
    {
        if ($language == null) {
            $language = HtmlComponent::getLanguage();
        }
       
        return (substr($path,-1) == "/") ? $path . "$language/" : "$path/$language/";
    }

    /**
     * Get curret page url
     *
     * @param boolean $full
     * @return string
     */
    public static function getCurrentUrl($full = true)
    {       
        $path = Session::get('current.path');

        return ($full == true) ? Self::getFullUrl($path) : $path;
    }

    /**
     * Return url link with current language code
     *
     * @param string $path
     * @param boolean $full
     * @param boolean $withLanguagePath
     * @return string
     */
    public static function getUrl($path = '', $full = false, $withLanguagePath = false)
    {       
        $path = (substr($path,0,1) == "/") ? substr($path,1) : $path;      
        $url = ($full == true) ? Url::BASE_URL : BASE_PATH;        
        $url = ($url == "/") ? $url : $url . "/";       

        return ($withLanguagePath == true) ? $url . Self::getLanguagePath($path) : $url . $path;
    }

    /**
     * Get full page url
     *
     * @param string $path
     * @return string
     */
    public static function getFullUrl($path)
    {
        return Self::getUrl($path,true);
    }

    /**
     * Include files
     *
     * @param Component $component
     * @return bool
     */
    public function includeFiles($component) 
    {
        $files = $this->getPageIncludeOptions($component);
        $files = Arrays::setDefault($files,'library',[]);            
        $files = Arrays::setDefault($files,'loader',false);       
              
        $this->includeComponents($component);

        $this->view->getCache()->save("page.include.files." . $component->getName(),$files,3);
        $this->view->properties()->set('template.files',$files);
        // include ui lib files                
        $this->includeLibraryFiles($files['library']);  
      
        // include template files               
        if (empty($files['template']) == false) {
            $this->includeThemeFiles($files['template']);  
        }
       
        return true;
    }

    /**
     * Return template files
     *
     * @return array
     */
    public function getTemplateFiles()
    {
        return $this->view->properties()->get('template.files');
    }

    /**
     * Return theme files
     *
     * @return array
     */
    public function getThemeFiles()
    {      
        return $this->view->properties()->get('template.theme');
    }

    /**
     * Return library files
     *
     * @return array
     */
    public function getLibraryFiles()
    {
        return $this->view->properties()->get('ui.library.files',[]);
    }

    /**
     * Get page include options
     *
     * @param Component $component
     * @return array
    */
    public function getPageIncludeOptions($component)
    {
        // from cache 
        $options = $this->view->getCache()->fetch("page.include.files." . $component->getName());
        if (is_array($options) == true) {
            return $options;
        }

        // from page options
        $options = $component->getOption('include',null);
      
        if (empty($options) == false) {  
            // get include options from page.json file  
            $options = Arrays::setDefault($options,'template',null);   
            $options = Arrays::setDefault($options,'js',[]);  
            $options = Arrays::setDefault($options,'css',[]);   

            $url = Url::getExtensionViewUrl($component->getTemplateName());
           
            $options['js'] = array_map(function($value) use($url) {              
                return $url . "/js/" . $value; 
            },$options['js']);
    
            $options['css'] = array_map(function($value) use($url) {
                return $url . "/css/" . $value;
            },$options['css']);

            if (empty($options['template']) == false) {
                $options = array_merge($options,$this->getTemplateIncludeOptions($options['template']));              
            } elseif ($component->getType() == ComponentData::TEMPLATE_COMPONENT) {
                $options = array_merge($options,$this->getTemplateIncludeOptions($component->getTemplateName())); 
            }                  
            // set loader from page.json
            if (isset($options['loader']) == true) {
                Session::set('template.loader',$options['loader']);
            }
           
            return $options;
        }

        // from component template 
        return $this->getTemplateIncludeOptions($component->getTemplateName());
    }

    /**
     * Include components fiels set in page.json include/components
     *
     * @param Component $component
     * @return void
     */
    protected function includeComponents($component)
    {
        // include component files
        $components = $component->getOption('include/components',null);        
        if (empty($components) == true) {
            return;
        }  
       
        foreach ($components as $item) {                        
            $files = $this->getComponentFiles($item);      
        
            $this->includeComponentFiles($files['js'],'js');
            $this->includeComponentFiles($files['css'],'css');              
        }      
    }

    /**
     * Get template include options
     *
     * @param string $templateName
     * @return array
     */
    public function getTemplateIncludeOptions($templateName)
    {
        $templateOptions = $this->packageFactroy->createPackage('template',$templateName)->getProperties();

        $options = $templateOptions->getByPath("include",[]);
    
        $options = Arrays::setDefault($options,'js',[]);  
        $options = Arrays::setDefault($options,'css',[]);   

        $url = Url::getTemplateUrl($templateName);    
      
        $options['js'] = array_map(function($value) use($url) {
            return $url . "/js/" . $value; 
        },$options['js']);

        $options['css'] = array_map(function($value) use($url) {
            return ResourceLocator::getResourceUrl($value,$url . "/css/" . $value);         
        },$options['css']);
      
        return $options;
    }

    /**
     * Include library files
     *
     * @param array $libraryList
     * @return bool
     */
    public function includeLibraryFiles(array $libraryList)
    {          
        $frameworks = [];
        $includeLib = [];

        foreach ($libraryList as $libraryName) {
            $library = $this->packageFactroy->createPackage('library',$libraryName);
            $files = $library->getFiles();       
            $params = $library->resolveParams();

            foreach($files as $file) {
                $libraryFile = $this->view->getViewPath() . 'library' . DIRECTORY_SEPARATOR . $libraryName . DIRECTORY_SEPARATOR . $file;
                $item = [
                    'file'        => (Utils::isValidUrl($file) == true) ? $file : Url::getLibraryFileUrl($libraryName,$file),
                    'type'        => File::getExtension($libraryFile),
                    'params'      => $params,
                    'library'     => $libraryName,
                    'async'       => $library->getProperties()->get('async',false),
                    'crossorigin' => $library->getProperties()->get('crossorigin',null)
                ];
                array_push($includeLib,$item);
            }           
            if ($library->isFramework() == true) {
                array_push($frameworks,$libraryName);
            }
        }

        $this->view->properties()->set('ui.library.files',$includeLib);       
        Session::set("ui.included.libraries",json_encode($libraryList));
        Session::set("ui.included.frameworks",json_encode($frameworks));

        return true;
    }

    /**
     * Include theme files
     *
     * @param string $templateName
     * @return bool
     */
    public function includeThemeFiles($templateName)
    {  
        // cehck cache
        $fileUrl = $this->view->getCache()->fetch('template.theme.file');
        if (empty($fileUrl) == false) {
            $this->view->properties()->add('template.theme',$fileUrl);
            return true;
        }
      
        $properties = $this->packageFactroy->createPackage('template',$templateName)->getProperties();
        $defaultTheme = $properties->get("default-theme",null);
        $currentTheme = Theme::getCurrentTheme($templateName,$defaultTheme);

        if (empty($currentTheme) == true) {
            return true;
        } 
        
        $library = $properties->getByPath("themes/$currentTheme/library","");
        $libraryPackage = $this->packageFactroy->createPackage('library',$library);
        // get theme from other template
        $template = $properties->getByPath("themes/$currentTheme/template","");
        $templateName = (empty($template) == false) ? $template : $templateName;
           
        if (empty($library) == false) {
            // load theme from library           
            $file = $libraryPackage->getThemeFile($currentTheme);
            $fileUrl = Url::getLibraryThemeFileUrl($library,$file,$currentTheme);
        } else {
            // load from template
            $file = $properties->getByPath("themes/$currentTheme/file","");
            $fileUrl = Url::getThemeFileUrl($templateName,$currentTheme,$file);
        }
        if (empty($fileUrl) == false) {
            $theme['name'] = $currentTheme;
            $theme['file'] = $file;
            $this->view->properties()->add('template.theme',$fileUrl);
            // saev to cache
            $this->view->getCache()->save('template.theme.file',$fileUrl,3);

            return true;
        }

        return false;
    }
}
