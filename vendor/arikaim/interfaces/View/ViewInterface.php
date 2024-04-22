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
     * Get service
     *
     * @param string $name
     * @return mixed|null
     */
    public function getService(string $name);
    
    /**
     * Get all global vars
     *
     * @return array
     */
    public function getGlobals(): array;

    /**
     * Add global variable
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $name, $value): void;
    
    /**
     * Get templates path
     *
     * @return string
     */
    public function getTemplatesPath(): string;
    
    /**
     * Get primary template
     *
     * @return string
     */
    public function getPrimaryTemplate(): string;

    /**
     * Set primary template
     *
     * @param string $templateName
     * @return void
     */
    public function setPrimaryTemplate(string $templateName): void;
    
    /**
     * Render template
     *
     * @param string $template
     * @param array $params
     * @return string|null
     */
    public function fetch(string $template, array $params = []): ?string;

    /**
     * Render template block
     *
     * @param string $template
     * @param string $block
     * @param array $params
     * @return string|null
     */
    public function fetchBlock(string $template, string $block, array $params = []): ?string;

    /**
     * Render template from string
     *
     * @param string $string
     * @param array $params
     * @return string
     */
    public function fetchFromString(string $string, array $params = []): string;

    /**
     * Get Twig environment
     *
     * @return \Twig\Environment
     */
    public function getEnvironment();

    /**
     * Get cache
     *
     * @return CacheInterface
     */
    public function getCache();

    /**
     * Gte extensions path
     *
     * @return string
     */
    public function getExtensionsPath(): string;

    /**
     * Get view path
     *
     * @return string
     */
    public function getViewPath(): string;

    /**
     * Get twig extension
     *
     * @return ExtensionInterface
     */
    public function getExtension(string $class);

    /**
     * Get current extension
     *
     * @return ExtensionInterface
     */
    public function getCurrentExtension();

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
    );

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
     * @return \Arikaim\Core\Interfaces\View\HtmlComponentInterface
    */
    public function renderComponent(
        string $name, 
        string $language, 
        ?array $params = [], 
        ?string $type = null,
        ?int $mode = null,
        array $parent = []
    );
}
