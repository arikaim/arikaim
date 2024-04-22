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
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Http\Url;

use Arikaim\Core\Interfaces\View\HtmlComponentInterface;
use Arikaim\Core\Interfaces\View\ComponentInterface;
use Arikaim\Core\Interfaces\View\ViewInterface;
use Arikaim\Core\Interfaces\View\EmailViewInterface;

use Arikaim\Core\View\Html\Component\Traits\Options;
use Arikaim\Core\View\Html\Component\Traits\Properties;
use Arikaim\Core\View\Html\Component\Traits\IndexPage;
use Arikaim\Core\View\Html\Component\Traits\UiLibrary;

/**
 * Render email component
 */
class EmailView extends BaseComponent implements HtmlComponentInterface, EmailViewInterface
{
    use 
        Options,
        IndexPage,
        UiLibrary,
        Properties;
  
    /**
     * Default language
     *
     * @var string
     */
    protected $defaultLanguage;

    /**
     * View 
     *
     * @var ViewInterface
     */
    protected $view;

    /**
     * Constructor
     *
     * @param ViewInterface $view
     * @param string $defaultLanguage    
     */
    public function __construct(ViewInterface $view, string $defaultLanguage) 
    {
        parent::__construct(
            '',
            'emails',
            'en',
            $view->getViewPath(),
            $view->getExtensionsPath(),
            $view->getPrimaryTemplate(),
            ComponentInterface::EMAIL_COMPONENT_TYPE
        );

        $this->defaultLanguage = $defaultLanguage;
        $this->view = $view;   
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
     * Init component
     *
     * @return void
     */
    public function init(): void 
    {        
        parent::init();

        $this->loadProperties();
        $this->loadOptions(); 
        $this->addComponentFile('css');           
        $this->hasHtmlContent = true; 
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
     * Get css email library name
     *
     * @return string
     */
    public function getLibraryName(): string
    {
        return $this->getOption('library','');
    }

    /**
     * Render email component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language    
     * @return \Arikaim\Core\Interfaces\View\EmailViewInterface|null
    */
    public function render(string $name, array $params = [], ?string $language = null)
    {
        $this->fullName = $name;
        $this->language = $language ?? $this->defaultLanguage;

        $this->init();
        
        if ($this->resolve($params) == false) { 
            return null;
        }
        // set current email component url
        $this->context['component_url'] = DOMAIN . $this->url;
        $this->context['template_url'] = Url::getTemplateUrl($this->getCurrentTemplate(),'/',false);
        $this->context['current_language'] = $this->language;

        $code = $this->view->fetch($this->getTemplateFile(),$this->getContext());

        if (Utils::hasHtml($code) == true) {
            // Email is html             
            $file = $this->getComponentFile('css');
            $componentCss = ($file !== false) ? File::read($this->getFullPath() . $file) : '';
            $params['library_css'] = [];
            $params['component_css'] = [];

            // included css library
            $library = $this->getLibraryName();  
            if (empty($library) == false) {
                $libraryCss = $this->readLibraryCode($library);
                $params['library_css'][] = $libraryCss;
            }
           
            if (empty($componentCss) == false) {
                $params['component_css'][] = $componentCss;                           
            }  
          
            if ($this->getOption('include-index',true) == true) {              
                $params['body'] = $code;
                $indexFile = $this->getIndexFile($this->primaryTemplate);
                $code = $this->view->fetch($indexFile,$params);                   
            } 
        }

        $this->setHtmlCode($code);   

        return $this;
    }   

    /**
     * Get email subject
     *
     * @return string
     */
    public function getSubject(): string
    {
       return $this->properties['subject'] ?? '';
    }

    /**
     * Read UI library css code
     *
     * @param string $name
     * @return string
     */
    public function readLibraryCode(string $name): string
    {
        list($libraryName,$libraryVersion) = $this->parseLibraryName($name);
        $properties = $this->getLibraryProperties($libraryName,$libraryVersion); 
        $content = '';

        foreach($properties['files'] as $file) {
            $libraryFile = Path::getLibraryFilePath($libraryName,$file);
            $content .= File::read($libraryFile);
        }

        return \trim($content);
    }    
}
