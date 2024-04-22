<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
*/
namespace Arikaim\Core\View;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

use Arikaim\Core\Interfaces\View\ViewInterface;
use Arikaim\Core\Interfaces\View\ComponentInterface;
use Arikaim\Core\Interfaces\View\RequireAccessInterface;

use Arikaim\Core\View\Traits\Access;
use Arikaim\Core\View\Traits\ThemeGlobals;
use Arikaim\Core\View\ComponentFactory;

/**
 * View class
 */
class View implements ViewInterface
{
    use 
        Access,
        ThemeGlobals;

    const COMPONENT_ERROR_NAME           = 'semantic~message.error';
    const ACCESS_DENIED_ERROR_CODE       = 'ACCESS_DENIED';
    const NOT_VALID_COMPONENT_ERROR_CODE = 'NOT_VALID_COMPONENT';
   
    /**
     * Template loader
     *
     * @var Twig\Loader\FilesystemLoader|null
     */
    private $loader = null;
    
    /**
     * Twig env
     *
     * @var \Twig\Environment|null
     */
    private $environment = null;

    /**
     * Cache
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * Vie wpath
     *
     * @var string
     */
    private $viewPath; 

    /**
     * Extensions Path
     *
     * @var string
     */
    private $extensionsPath;

    /**
     * Templates path
     *
     * @var string
     */
    private $templatesPath;

    /**
     * Components path
     *
     * @var string
     */
    private $componentsPath;

    /**
     * Current extension class
     *
     * @var string|null
     */
    private $currentExtensionClass = null;

    /**
     * Twig view settigns
     *
     * @var array
     */
    private $settings = [];

    /**
     * Primary template
     *
     * @var string
     */
    protected $primaryTemplate;

    /**
     * Template theme
     *
     * @var string|null
     */
    protected $templateTheme;

    /**
     * Services
     *
     * @var array
     */
    protected $services = [];

    /**
     * Constructor
     *
     * @param CacheInterface $cache
     * @param array $services
     * @param string $viewPath
     * @param string $extensionsPath
     * @param string $templatesPath
     * @param string $componentsPath
     * @param array $settings
     * @param string|null $primaryTemplate
     */
    public function __construct(
        object $cache,           
        string $viewPath,
        string $extensionsPath,
        string $templatesPath,
        string $componentsPath,
        array $services = [],   
        array $settings = [],
        ?string $primaryTemplate = null,
        ?string $templateTheme = null)
    {
        $this->viewPath = $viewPath;      
        $this->extensionsPath = $extensionsPath;
        $this->templatesPath = $templatesPath;
        $this->componentsPath = $componentsPath;       
        $this->settings = $settings;      
        $this->cache = $cache;      
        $this->services  = $services;
        $this->primaryTemplate = $primaryTemplate ?? 'system';       
        $this->templateTheme = $templateTheme;
    }

    /**
     * Get service
     *
     * @param string $name
     * @return mixed|null
     */
    public function getService(string $name)
    {
        return $this->services[$name] ?? null;
    }

    /**
     * Create component
     *
     * @param string $name
     * @param string $language
     * @param string $type
     * @param int|null $renderMode
     * @param array $parent 
     * 
     * @return Arikaim\Core\Interfaces\View\ComponentInterface
     */
    public function createComponent(
        string $name, 
        string $language, 
        string $type, 
        ?int $renderMode = null,
        array $parent = []
    )
    {             
        return ComponentFactory::create(
            $name,
            $language,
            $type,$this->viewPath,
            $this->extensionsPath,
            $this->primaryTemplate,
            $renderMode,
            $parent
        );
    }

    /**
     * Render html component
     *
     * @param string $name
     * @param string $language
     * @param array|null $params
     * @param string|null $type
     * @param int|null $mode
     * @param array $parent
     * 
     * @return Arikaim\Core\Interfaces\View\ComponentInterface
    */
    public function renderComponent(
        string $name, 
        string $language, 
        ?array $params = [], 
        ?string $type = null,
        ?int $mode = null,
        array $parent = []
    )
    {
        $type = $type ?? ComponentInterface::ARIKAIM_COMPONENT_TYPE;
        $cacheItemName = 'html.component.' . $name . '.' . $language;        
        $cached = $this->cache->fetch($cacheItemName);

        $component = ($cached === false) ? $this->createComponent($name,$language,$type,$mode,$parent) : $cached;

        if ($component instanceof RequireAccessInterface) {
            if ($this->checkAccessOption($component) == false) {              
                return $this->renderComponentError($name,$language,Self::ACCESS_DENIED_ERROR_CODE,$component->getOptions());
            }              
        }

        if ($component->resolve($params) == false) {
            return $this->renderComponentError($name,$language,Self::NOT_VALID_COMPONENT_ERROR_CODE,[]);
        }

        if ($component->hasContent() == true) {
            $html = $this->fetch($component->getTemplateFile(),$component->getContext());
            $component->setHtmlCode($html);  
        }

        // check for edit mode
        if ($mode == ComponentInterface::RENDER_MODE_EDIT) {
            $editorComponent = $component->getContext()['_editor']['component'] ?? null;
            if (empty($editorComponent) == false) {
                $params['edit_component'] = $component->getContext();
                $params['edit_component_name'] = $component->getName();
                $params['edit_component_html'] = $component->getHtmlCode();

                return $this->renderComponent($editorComponent,$language,$params);
            }                
        }

        if (
            ($cached === false) && 
            ($type == ComponentInterface::SVG_COMPONENT_TYPE || $type == ComponentInterface::STATIC_COMPONENT_TYPE) 
        ) 
        {
            // save cache
            $this->cache->save($cacheItemName,$component);
        }
            
        return $component;
    }

    /**
     * Render compoent error
     *
     * @param string $name
     * @param string $language
     * @param array $options
     * @return Arikaim\Core\Interfaces\View\ComponentInterface
    */
    protected function renderComponentError(string $name, string $language, string $errorCode, array $options = [])
    {
        $component = $this->renderComponent(Self::COMPONENT_ERROR_NAME,$language,[
            'message' => 'Error in html component <b>' . $name . '</b>' . $errorCode
        ],'static');
        $component->setError($errorCode);
        $component->setOption('redirect',$options['access']['redirect'] ?? null);

        return $component;
    } 

    /**
     * Get extension funciton
     *
     * @param string|null $name
     * @return object|null
     */
    public function getFunction(?string $name = null)
    {
        $functions = $this->getEnvironment()->getFunctions();

        return $functions[$name] ?? null;
    }
    
    /**
     * Get primary template
     *
     * @return string
     */
    public function getPrimaryTemplate(): string
    {              
        return $this->primaryTemplate;
    }

    /**
     * Set primary template
     *
     * @param string $templateName
     * @return void
     */
    public function setPrimaryTemplate(string $templateName): void
    {       
        $this->primaryTemplate = $templateName;
    }

    /**
     * Get all global vars
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return $this->getEnvironment()->getGlobals();
    }

    /**
     * Add global variable
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $name, $value): void
    {
        $this->getEnvironment()->addGlobal($name,$value);
    }

    /**
     * Return true if global vari exist
     *
     * @param string $name
     * @return boolean
     */
    public function hasGlobal(string $name): bool
    {
        return \array_key_exists($name, $this->getEnvironment()->getGlobals());
    }  

    /**
     * Get global variable
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getGlobalVar(string $name, $default = null)
    {
        $globals = $this->getEnvironment()->getGlobals();

        return $globals[$name] ?? $default;
    }

    /**
     * Get components path
     *
     * @return string
     */
    public function getComponentsPath(): string
    {
        return $this->componentsPath;
    }

    /**
     * Get templates path
     *
     * @return string
     */
    public function getTemplatesPath(): string
    {
        return $this->templatesPath;
    }

    /**
     * Gte extensions path
     *
     * @return string
     */
    public function getExtensionsPath(): string
    {
        return $this->extensionsPath;
    }

    /**
     * Get view path
     *
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * Get cache
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Add template extension
     *
     * @param ExtensionInterface $extension
     * @return void
     */
    public function addExtension(ExtensionInterface $extension): void
    {
        $this->getEnvironment()->addExtension($extension);
        $this->currentExtensionClass = \get_class($extension);
    }

    /**
     * Render template
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function fetch(string $template, array $params = []): string
    {       
        return $this->getEnvironment()->load($template)->render($params);
    }

    /**
     * Render template block
     *
     * @param string $template
     * @param string $block
     * @param array $params
     * @return string|null
     */
    public function fetchBlock(string $template, string $block, array $params = []): ?string
    {
        return $this->getEnvironment()->load($template)->renderBlock($block,$params);
    }

    /**
     * Render template from string
     *
     * @param string $string
     * @param array $params
     * @return string
     */
    public function fetchFromString(string $string, array $params = []): string
    {
        return $this->getEnvironment()->createTemplate($string)->render($params);
    }

    /**
     * Get twig extension
     *
     * @return ExtensionInterface
     */
    public function getExtension(string $class)
    {
        return $this->getEnvironment()->getExtension($class);
    }

    /**
     * Get current extension (last added)
     *
     * @return ExtensionInterface
     */
    public function getCurrentExtension()
    {
        return $this->getExtension($this->currentExtensionClass);
    }

    /**
     * Get Twig loader
     *
     * @return Twig\Loader\FilesystemLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Clear current env
     *
     * @return void
     */
    public function destroyEnvironment():void 
    {
        $this->environment = null;
    }

    /**
     * Get Twig environment
     *
     * @return \Twig\Environment
     */
    public function getEnvironment()
    {
        if ($this->environment == null) {
            $this->resolveEnvironment();
        }

        return $this->environment;
    }

    /**
     * Create twig environment
     *
     * @param array|null $paths
     * @param array|null $settings
     * @return Environment
     */
    public function createEnvironment(?array $paths = null, ?array $settings = null)
    {
        $loader = new FilesystemLoader($paths ?? [
            $this->extensionsPath,
            $this->templatesPath,
            $this->componentsPath
        ]);
         
        return new Environment($loader,$settings ?? $this->settings);          
    }

    /**
     * Create env instance
     *
     * @return void
     */
    protected function resolveEnvironment(): void
    {
        $this->environment = $this->createEnvironment();
        
        $this->environment->addGlobal('demo_mode',$this->settings['demo_mode'] ?? false);      
        $this->environment->addGlobal('current_language',null);      
        $this->environment->addGlobal('current_url_path',null);      

        // add theme globals
        $this->includeThemeGlobals($this->templateTheme);
    }
}
