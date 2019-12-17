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

use Twig\Environment;

use Arikaim\Core\Http\Session;
use Arikaim\Core\Utils\Mobile;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\View\Html\ComponentData;
use Arikaim\Core\View\Interfaces\ComponentDataInterface;
use Arikaim\Core\Interfaces\View\ViewInterface;

/**
 *  Base html component
 */
class Component   
{
    /**
     * Twig view
     *
     * @var ViewInterface
     */
    protected $view;

    /**
     * Cache
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Component params
     *
     * @var array
     */
    protected $params;

    /**
     * Language
     *
     * @var string
     */
    protected $language;
    
    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Component name
     *
     * @var string
     */
    protected $name;

    /**
     * Component data
     *
     * @var Arikaim\Core\View\Interfaces\ComponentDataInterface
     */
    protected $componentData;

    /**
     * Options file name
     *
     * @var string
     */
    protected $optionsFile;

    /**
     * Constructor
     *
     * @param ViewInterface $view
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @param string $basePath
     * @param string|null $optionsFile
     * @param boolean $withOptions
     */
    public function __construct(ViewInterface $view, $name, $params = [], $language = null, $basePath = 'components', $optionsFile = null, $withOptions = true)
    {
        $this->view = $view;
        $this->basePath = $basePath;
        $this->withOptions = $withOptions;
        $this->optionsFile = (empty($optionsFile) == true) ? 'component.json' : $optionsFile;
        $this->name = $name;
        $this->language = (empty($language) == true) ? Self::getLanguage() : $language;
        $this->params = $params;

        if (empty($name) == false) {
            $this->componentData = $this->createComponentData($name,$language,$withOptions);
        }
    }

    /**
     * Create component data obj
     *
     * @param string $name
     * @param string|null $language
     * @param boolean $withOptions
     * @return ComponentData
     */
    protected function createComponentData($name, $language = null, $withOptions = true)
    {
        $language = (empty($language) == true) ? $this->language : $language;

        $componentData = new ComponentData($name,$this->basePath,$language,$this->optionsFile,$this->view->getViewPath(),$this->view->getExtensionsPath());
        if ($componentData->isValid() == false) {           
            $componentData->setError("TEMPLATE_COMPONENT_NOT_FOUND",["name" => $name]);             
        }
        $componentData = ($withOptions == true) ? $this->processOptions($componentData) : $componentData;  
        
        return $componentData;
    }

    /**
     * Fetch component
     *
     * @param Environment $env
     * @param ComponentDataInterface $component
     * @param array $params
     * @return Component
     */
    public function fetch(ComponentDataInterface $component, $params = [])
    {
        if (empty($component->getTemplateFile()) == true) {
            return $component;
        }
        $this->view->getEnvironment()->loadTemplate($component->getTemplateFile());
    
        $code = $this->view->getEnvironment()->render($component->getTemplateFile(),$params);
        $component->setHtmlCode($code);    

        return $component;
    }

    /**
     * Procss component options
     *
     * @param ComponentDataInterface $component
     * @return Arikaim\Core\View\Interfaces\ComponentDataInterface
     */
    public function processOptions(ComponentDataInterface $component)
    {        
        $error = false;       
        // check auth access 
        $auth = $component->getOption('access/auth');
        if (empty($auth) == false && strtolower($auth) != 'none') {
            $access = $this->view->getExtension("Arikaim\\Core\\View\\Template\\Extension")->isLogged();
            if ($access == false) {
                $component->setError("ACCESS_DENIED",["name" => $component->getName()]);
            }
        } else {
            // check root component auth access option
            
        }

        // check permissions
        $permission = $component->getOption('access/permission');       
        if (empty($permission) == false) {
            $access = $this->view->getExtension("Arikaim\\Core\\View\\Template\\Extension")->hasAccess($permission);
            if ($access == false) {              
                $component->setError("ACCESS_DENIED",["name" => $component->getName()]);
            }          
        } else {
            // check root component permissions

        }    
        
        $component = Self::applyIncludeOption($component,'include/js','js');
        $component = Self::applyIncludeOption($component,'include/css','css');

        // mobile only option
        $mobileOnly = $component->getOption('mobile-only');      
        if ($mobileOnly == "true") {
            if (Mobile::mobile() == false) {    
                $component->clearContent();               
            }
        }

        return $component;
    }

    /**
     * Apply component include option
     *
     * @param Arikaim\Core\View\Interfaces\ComponentDataInterface $component
     * @param string $key
     * @param string $fileType
     * @return Arikaim\Core\View\Interfaces\ComponentDataInterface
     */
    protected function applyIncludeOption(ComponentDataInterface $component, $key, $fileType)
    { 
        $option = $component->getOption($key);   
       
        if (empty($option) == false) {
            if (is_array($option) == true) {              
                // include component files
                foreach ($option as $item) {                      
                    $files = $this->resolveIncludeFile($item,$fileType);
                    $component->addFiles($files,$fileType);
                }
            } else {   
                $files = $this->resolveIncludeFile($option,$fileType);                        
                $component->addFiles($files,$fileType);
            }
        }
        
        return $component;
    }

    /**
     * Resolve include file
     *
     * @param string $includeFile
     * @param string $fileType
     * @return array
     */
    protected function resolveIncludeFile($includeFile, $fileType)
    {
        if (Utils::isValidUrl($includeFile) == true) {             
            $tokens = explode('|',$includeFile);
            $url = $tokens[0];
            $tokens[0] = 'external';
            $params = (isset($tokens[1]) == true) ? $tokens : [];                           
            $files = [['url' => $url,'params' => $params]];       
        } else {
            $files = $this->getComponentFiles($includeFile,$fileType);
        }

        return $files;
    }

    /**
     * Return compoenent files
     *
     * @param string $name
     * @param string $fileType
     * @return array
     */
    public function getComponentFiles($name, $fileType = null)
    {        
        $componentData = new ComponentData($name,'components',null,'component.json',$this->view->getViewPath(),$this->view->getExtensionsPath());
        
        return (is_object($componentData) == true) ? $componentData->getFiles($fileType) : ['js' => [],'css' => []];
    }

    /**
     * Return true if component is vlaid
     *
     * @return boolean
     */
    public function isValid()
    {
        $this->componentData->isValid();
    }

    /**
     * Return true if component have content
     *
     * @return boolean
     */
    public function hasContent()
    {
        return $this->componentData->hasContent();
    }
    
    /**
     * Get component options
     *
     * @param string $name   
     * @return array|null
     */
    public function getOptions()
    {       
        return $this->componentData->getOptions();
    }

    /**
     * Inlcude componnent files
     *
     * @param array $files
     * @param string $key
     * @return boolean
     */
    public function includeComponentFiles($files, $key)
    {
        if (empty($files) == true) {
            return false;
        }       
        foreach ($files as $item) {             
            $this->view->properties()->prepend('include.components.files',$item,$key);                    
        }

        return true;
    }

    /**
     * Return true if component name is full name
     *
     * @param string $name
     * @return boolean
     */
    public static function isFullName($name)
    {
        return (stripos($name,':') !== false || stripos($name,'>') !== false) ? true : false;          
    } 

    /**
     * Return current language
     *
     * @return string
     */
    public static function getLanguage() 
    {  
        return Session::get('language',"en");
    }

    /**
     * Set current language
     *
     * @param string $language Language code
     * @return void
     */
    public static function setLanguage($language) 
    {
        Session::set('language',$language);
    }
}
