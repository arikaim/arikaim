<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Http;

/**
 * Url helper
 */
class Url
{   
    const BASE_URL         = DOMAIN . BASE_PATH;  
    const VIEW_URL         = APP_URL . '/view';
    const EXTENSIONS_URL   = APP_URL . '/extensions';
    const LIBRARY_URL      = Self::VIEW_URL . '/library';
    const TEMPLATES_URL    = Self::VIEW_URL . '/templates';
    const COMPONENTS_URL   = Self::VIEW_URL . '/components';
  
    /**
     * Init domain and base path constants
     *
     * @param string $domain
     * @param string $basePath
     * @return void
     */
    public static function init($domain,$basePath)
    {
        if (defined('DOMAIN') == false) {
            define('DOMAIN',$domain);
        }

        if (defined('BASE_PATH') == false) {
            define('BASE_PATH',$basePath);
        }
    }

    /**
     * Set app url
     *
     * @param string $path
     * @return void
     */
    public static function setAppUrl($path) 
    {
        if (defined('APP_URL') == false) {
            define('APP_URL',Self::BASE_URL . $path);
        }       
    }

    /**
     * Get theme file url
     *
     * @param string $template
     * @param string $theme
     * @param string $themeFile
     * @return string
     */
    public static function getThemeFileUrl($template, $theme, $themeFile)
    {
        return (empty($themeFile) == true) ? null : Self::getTemplateThemeUrl($template,$theme) . $themeFile;       
    }

    /**
     * Get template theme url
     *
     * @param string $template
     * @param string $theme
     * @return string
     */
    public static function getTemplateThemeUrl($template, $theme)
    {
        return Self::getTemplateThemesUrl($template) . "/$theme/";
    }

    /**
     * Get template url
     *
     * @param string $template
     * @return string
     */
    public static function getTemplateUrl($template) 
    {       
        return Self::TEMPLATES_URL . "/$template";       
    }

    /**
     * Get template themes url
     *
     * @param string $template
     * @return string
     */
    public static function getTemplateThemesUrl($template)
    {
        return Self::getTemplateUrl($template) . "/themes";
    }
    
    /**
     * Get UI library themes url
     *
     * @param string $library
     * @return string
     */
    public static function getLibraryThemesUrl($library)
    {
        return Self::getLibraryUrl($library) . "/themes";
    }

    /**
     * Get UI library theme url
     *
     * @param string $library
     * @param string $theme
     * @return string
     */
    public static function getLibraryThemeUrl($library, $theme)
    {
        return Self::getLibraryUrl($library) . "/themes/$theme/";
    }

    /**
     * Get UI library theme file url
     *
     * @param string $library
     * @param string $file
     * @param string $theme
     * @return string
     */
    public static function getLibraryThemeFileUrl($library, $file, $theme)
    {
        return Self::getLibraryThemeUrl($library,$theme) . $file;
    }

    /**
     * Get UI library url
     *
     * @param string $library
     * @return string
     */
    public static function getLibraryUrl($library)
    {
        return Self::LIBRARY_URL . "/$library";
    }

    /**
     * Get UI library file url
     *
     * @param string $library
     * @param string $fileName
     * @return string
     */
    public static function getLibraryFileUrl($library, $fileName)
    {
        return Self::getLibraryUrl($library) . "/$fileName";
    }

    /**
     * Get extension view url
     *
     * @param string $extension
     * @return string
     */
    public static function getExtensionViewUrl($extension)
    {
        return Self::EXTENSIONS_URL . "/$extension/view";
    }

    /**
     * Return true if url is valid
     *
     * @param string $url
     * @return boolean
     */
    public static function isValid($url)
    {
        return (filter_var($url,FILTER_VALIDATE_URL) == true) ? true : false; 
    }
}
