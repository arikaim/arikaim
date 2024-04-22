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
 * View component interface
 */
interface ComponentInterface 
{  
    // render mode
    const RENDER_MODE_VIEW = 0;
    const RENDER_MODE_EDIT = 1;

    // component locations
    const UNKNOWN_COMPONENT   = 0;
    const TEMPLATE_COMPONENT  = 1; 
    const EXTENSION_COMPONENT = 2;
    const PRIMARY_TEMLATE     = 3;
    const COMPONENTS_LIBRARY  = 4; 

    // component types
    const PAGE_COMPONENT_TYPE    = 'page';
    const ARIKAIM_COMPONENT_TYPE = 'arikaim';
    const VUE_COMPONENT_TYPE     = 'vue';
    const REACT_COMPONENT_TYPE   = 'react';
    const STATIC_COMPONENT_TYPE  = 'static';
    const EMAIL_COMPONENT_TYPE   = 'email';
    const SVG_COMPONENT_TYPE     = 'svg';
    const JSON_COMPONENT_TYPE    = 'json';
    const EMPTY_COMPONENT_TYPE   = 'empty';
    const HTML_COMPONENT_TYPE    = 'html';
    const JS_COMPONENT_TYPE      = 'js';
    const WIWGET_COMPONENT_TYPE  = 'widget';

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions(): array;
    
    /**
     * Add included component
     *
     * @param string $name
     * @param string $type
     * @param string|null $id
     * @return void
     */
    public function addIncludedComponent(string $name, string $type, ?string $id = null);
    
    /**
     * Get included components
     *
     * @return array
     */
    public function getIncludedComponents(): array;
    
    /**
     * Set context
     *
     * @param array $context
     * @return void
     */
    public function setContext(array $context): void;

    /**
     * Get context
     *
     * @return array
     */
    public function getContext(): array;

    /**
     * Get include file url
     *
     * @param string $fileType
     * @return string|null
     */
    public function getIncludeFile(string $fileType): ?string;

    /**
     * Add file
     *
     * @param array $file
     * @param string $fileType
     * @return void
     */
    public function addFile(array $file, string $fileType): void;

    /**
     * Get component file
     *
     * @param string $fileExt  
     * @return string|false
     */
    public function getComponentFile(string $fileExt);
    
    /**
     * Get full path
     *
     * @return string
     */
    public function getFullPath(): string;

    /**
     * Convert to array
     *
     * @return array
    */
    public function toArray(): array;

    /**
     * Set primary template name
     *
     * @param string $name
     * @return void
     */
    public function setPrimaryTemplate(string $name): void;
    
    /**
     * Get language code
     *
     * @return string
     */
    public function getLanguage(): string;

    /**
     * Get error
     *
     * @return string|null
     */
    public function getError(): ?string;

    /**
     * Return true if component have error
     *
     * @return boolean
     */
    public function hasError(): bool;

    /**
     * Return true if component is not empty
     *
     * @return boolean
     */
    public function hasContent(): bool;
 
    /**
     * Return component files 
     *
     * @param string $fileType
     * @return array
     */
    public function getFiles(?string $fileType = null): array;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get component location
     *
     * @return integer
     */
    public function getLocation(): int;

    /**
     * Set component type
     *
     * @param string $type
     * @return void
     */
    public function setComponentType(string $type): void;

    /**
     * Get component type
     *
     * @return string
     */
    public function getComponentType(): string;

    /**
     * Check if component is valid 
     *
     * @return boolean
     */
    public function isValid(): bool;

    /**
     * Get component html code
     *
     * @return string
     */
    public function getHtmlCode(): string;

    /**
     * Get url
     *
     * @return string
     */
    public function url(): string;

    /**
     * Get template file
     *
     * @return string|false
     */
    public function getTemplateFile(): ?string;

    /**
     * Set html code
     *
     * @param string $code
     * @return void
     */
    public function setHtmlCode(string $code): void;

    /**
     * Set error
     *
     * @param string $code    
     * @return void
     */
    public function setError(string $code): void;

    /**
     * Clear content
     *
     * @return void
     */
    public function clearContent(): void;    

    /**
     * Get template url
     *
     * @return string
     */
    public function getTemplateUrl(): string;

    /**
     * Get component full name
     *
     * @return string
     */
    public function getFullName(): ?string;

    /**
     * Get template or extension name
     *
     * @return string|null
     */
    public function getTemplateName(): ?string;

    /**
     * Return base path
     *
     * @return string
     */
    public function getBasePath(): string;    
}
