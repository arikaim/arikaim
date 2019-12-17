<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\View;

/**
 * View interface
 */
interface ViewInterface
{    
    /**
     * Render template
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function fetch($template, $params = []);

    /**
     * Render template block
     *
     * @param string $template
     * @param string $block
     * @param array $params
     * @return string
     */
    public function fetchBlock($template, $block, $params = []);

    /**
     * Render template from string
     *
     * @param string $string
     * @param array $params
     * @return string
     */
    public function fetchFromString($string, $params = []);

    /**
     * Get Twig environment
     *
     * @return Twig\Environment
     */
    public function getEnvironment();

    /**
     * Get cache
     *
     * @return CacheInterface
     */
    public function getCache();

    /**
     * Get properties
     *
     * @return Collection
    */
    public function properties();

     /**
     * Gte extensions path
     *
     * @return string
     */
    public function getExtensionsPath();

    /**
     * Get view path
     *
     * @return string
     */
    public function getViewPath();

    /**
     * Get twig extension
     *
     * @return ExtensionInterface
     */
    public function getExtension($class);
}
