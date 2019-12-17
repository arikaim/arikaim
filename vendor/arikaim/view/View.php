<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Interfaces\View\ViewInterface;
use Arikaim\Core\Interfaces\CacheInterface;

/**
 * View class
 */
class View implements ViewInterface
{
    /**
     * Template loader
     *
     * @var Twig\Loader\FilesystemLoader
     */
    private $loader;
    
    /**
     * Twig env
     *
     * @var Twig\Environment
     */
    private $environment;

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
     * Page properties collection
     *
     * @var Collection
     */
    private $pageProperties;

    /**
     * Constructor
     *
     * @param CacheInterface $cache
     * @param string $viewPath
     * @param string $extensionsPath
     * @param string $templatesPath
     * @param string $componentsPath
     * @param array $settings
     */
    public function __construct(CacheInterface $cache, $viewPath, $extensionsPath, $templatesPath, $componentsPath, $settings = [])
    {
        $this->pageProperties = new Collection();
        $this->viewPath = $viewPath;
        $this->extensionsPath = $extensionsPath;
        $this->templatesPath = $templatesPath;
        $this->componentsPath = $componentsPath;
      
        $paths = [
            $extensionsPath,
            $templatesPath,
            $componentsPath
        ];

        $this->loader = $this->createLoader($paths);  
        $this->cache = $cache;      
        $this->environment = new Environment($this->loader,$settings);
        $this->environment->addGlobal('current_component_name','');
    }

    /**
     * Get page properties
     *
     * @return Collection
     */
    public function properties()
    {
        return $this->pageProperties;
    }

    /**
     * Gte extensions path
     *
     * @return string
     */
    public function getExtensionsPath()
    {
        return $this->extensionsPath;
    }

    /**
     * Get view path
     *
     * @return string
     */
    public function getViewPath()
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
    public function addExtension(ExtensionInterface $extension)
    {
        $this->environment->addExtension($extension);
    }

    /**
     * Render template
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function fetch($template, $params = [])
    {       
        return $this->environment->render($template,$params);
    }

    /**
     * Render template block
     *
     * @param string $template
     * @param string $block
     * @param array $params
     * @return string
     */
    public function fetchBlock($template, $block, $params = [])
    {
        return $this->environment->loadTemplate($template)->renderBlock($block,$params);
    }

    /**
     * Render template from string
     *
     * @param string $string
     * @param array $params
     * @return string
     */
    public function fetchFromString($string, $params = [])
    {
        return $this->environment->createTemplate($string)->render($params);
    }

    /**
     * Get twig extension
     *
     * @return ExtensionInterface
     */
    public function getExtension($class)
    {
        return $this->environment->getExtension($class);
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
     * Get Twig environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Add path to loader
     *
     * @param string $path
     * @return void
     */
    public function addPath($path)
    {
        return $this->environment->getLoader()->addPath($path); 
    }

    /**
     * Create template loader
     *
     * @param array $paths
     * @return FilesystemLoader
     */
    private function createLoader($paths)
    {
        $paths = (is_array($paths) == false) ? $paths = [$paths] : $paths;
        
        return new FilesystemLoader($paths);
    }
}
