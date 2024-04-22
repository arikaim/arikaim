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
    const RELATIVE_APP_URL  = BASE_PATH . '/arikaim';
    const BASE_URL          = DOMAIN . BASE_PATH;  
    const VIEW_URL          = Self::RELATIVE_APP_URL . '/view';
    const EXTENSIONS_URL    = Self::RELATIVE_APP_URL . '/extensions';
    const LIBRARY_URL       = Self::VIEW_URL . '/library';
    const TEMPLATES_URL     = Self::VIEW_URL . '/templates';
    const COMPONENTS_URL    = Self::VIEW_URL . '/components';
  
    /**
     * Replace url query vars with params array
     *
     * @param string $url
     * @param array $params
     * @return string|null
     */
    public static function replaceUrlQueryParams(string $url, array $params): ?string
    {
        $urlParts = \parse_url($url);
        if ($urlParts === false) {
            return null;
        }
        $port = $urlParts['port'] ?? '';       
        $path = $urlParts['path'] ?? '';
        $query = $urlParts['query'] ?? '';
        $fragment = $urlParts['fragment'] ?? '';

        $result = $urlParts['scheme'] . '://' . $urlParts['host'];
        $result = (empty($port) == false) ? $result . ':' . $port : $result;
        $result .= $path;

        if (empty($query) == false) {
            // replce query vars values
            \parse_str($query,$queryVars);
            foreach ($queryVars as $key => $value) {
                $queryVars[$key] = $params[$key] ?? $value;               
            }
            $result .= '?' . \http_build_query($queryVars);          
        }
        $result = (empty($fragment) == false) ? $result . '#' . $fragment : $result;

        return $result;
    }

    /**
     * Remove url schema
     *
     * @param string $url
     * @return string
     */
    public static function removeSchema(string $url): string
    {
        $schema = \parse_url($url,PHP_URL_SCHEME);
        $url = \str_replace($schema,'',$url);
        $url = \str_replace("://",'',$url);

        return \str_replace($schema,'',$url);
    }

    /**
     * Get url file name
     *
     * @param string $url
     * @return string|null
    */
    public static function getUrlFileName(string $url): ?string
    {
        $path = \parse_url($url,PHP_URL_PATH);
        if (empty($path) == true) {
            return null;
        }
        $tokens = \explode('/',$path);

        return end($tokens);
    }

    /**
     * Return url link with current language code
     *
     * @param string $path
     * @param boolean $relative
     * @param string|null $language
     * @param string|null $defaultLanguage
     * @return string
     */
    public static function getUrl(?string $path = '', bool $relative = false, ?string $language = null, ?string $defaultLanguage = null): string
    {       
        $defaultLanguage = $defaultLanguage ?? $language;
        $path = (\substr($path,0,1) == '/') ? \substr($path,1) : $path;      
        $url = ($relative == false) ? Url::BASE_URL : BASE_PATH;        
        $url = ($url == '/') ? $url : $url . '/';   
        $url .= $path;       
        if (empty($language) == true) {
            return $url;
        }
        
        return ($defaultLanguage != $language) ? Self::getLanguagePath($url,$language) : $url;
    }

    /**
     * Get language path
     *
     * @param string $path
     * @param string $language
     * @return string
     */
    public static function getLanguagePath(string $path, string $language): string
    {   
        return (\substr($path,-1) == '/') ? $path . $language . '/' : $path . '/' . $language . '/';
    }

    /**
     * Retrun true if url is relative
     *
     * @param string $url
     * @return boolean
     */
    public static function isRelative(?string $url): bool
    {
        return (\strpos($url,DOMAIN) === false);
    }

    /**
     * Get url query param value
     *
     * @param string|null $url
     * @param string $paramName
     * @return string|null
     */
    public static function getUrlParam(?string $url, string $paramName): ?string
    {
        $parts = \parse_url($url);
        \parse_str($parts['query'],$query);

        return $query[$paramName] ?? null;
    }

    /**
     * Return true if url is remote server
     *
     * @param string $url
     * @return boolean
     */
    public static function isRemote(string $url): bool
    {
        if (Self::isValid($url) == false) {
            return false;
        }
        $info = \parse_url($url);
        $host = $info['host'] ?? '';

        return ($host != DOMAIN);
    }

    /**
     * Init domain and base path constants
     *
     * @param string $domain
     * @param string $basePath
     * @return void
     */
    public static function init(string $domain, ?string $basePath): void
    {
        if (\defined('DOMAIN') == false) {
            \define('DOMAIN',$domain);
        }

        if (\defined('BASE_PATH') == false) {
            \define('BASE_PATH',$basePath);
        }
    }

    /**
     * Set app url
     *
     * @param string $path
     * @return void
     */
    public static function setAppUrl(string $path): void 
    {
        if (\defined('APP_URL') == false) {
            \define('APP_URL',Self::BASE_URL . $path);
        }       
    }

    /**
     * Get theme file url
     *
     * @param string $template
     * @param string $theme
     * @param string|null $themeFile
     * @return string|null
     */
    public static function getThemeFileUrl(string $template, string $theme, ?string $themeFile): ?string
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
    public static function getTemplateThemeUrl(string $template, string $theme): string
    {
        return Self::getTemplateThemesUrl($template) . '/' . $theme . '/';
    }

    /**
     * Get template url
     *
     * @param string $template
     * @param string $path
     * @param bool $relative
     * @return string
     */
    public static function getTemplateUrl(string $template, string $path = '', bool $relative = true): string 
    {       
        $url = Self::TEMPLATES_URL . '/' . $template . $path;   
        
        return ($relative == false) ? DOMAIN . $url : $url;
    }

    /**
     * Get components library url
     *
     * @param string $name
     * @param string $path
     * @param bool $relative
     * @return string
     */
    public static function getComponentsLibraryUrl(string $name, string $path = '', bool $relative = true): string 
    {       
        $url = Self::COMPONENTS_URL . '/' . $name . $path;   
        
        return ($relative == false) ? DOMAIN . $url : $url;
    }

    /**
     * Get template themes url
     *
     * @param string $template
     * @return string
     */
    public static function getTemplateThemesUrl(string $template): string
    {
        return Self::getTemplateUrl($template) . '/themes';
    }
    
    /**
     * Get UI library theme url
     *
     * @param string $library
     * @param string $theme
     * @return string
     */
    public static function getLibraryThemeUrl(string $library, string $theme): string
    {
        return Self::getLibraryUrl($library) . '/themes/' . $theme . '/';
    }

    /**
     * Get UI library theme file url
     *
     * @param string $library
     * @param string $file
     * @param string $theme
     * @return string
     */
    public static function getLibraryThemeFileUrl(string $library, string $file, string $theme): string
    {
        return Self::getLibraryThemeUrl($library,$theme) . $file;
    }

    /**
     * Get UI library url
     *
     * @param string $library
     * @param bool $relative
     * @return string
     */
    public static function getLibraryUrl(string $library, bool $relative = true): string
    {
        $url = Self::LIBRARY_URL . '/' . $library;

        return ($relative == false) ? DOMAIN . $url : $url;
    }

    /**
     * Get UI library file url
     *
     * @param string $library
     * @param string|null $fileName
     * @param array|null $params
     * @return string
     */
    public static function getLibraryFileUrl(string $library, string $fileName = '', ?array $params = null): string
    {
        $paramsText = (empty($params) == false) ? '?' . \http_build_query($params) : '';

        return Self::getLibraryUrl($library) . '/' . $fileName . $paramsText;
    }

    /**
     * Get extension view url
     *
     * @param string $extension
     * @param string $path
     * @param bool $relative
     * @return string
     */
    public static function getExtensionViewUrl(string $extension, string $path = '', bool $relative = true): string
    {
        $url = Self::EXTENSIONS_URL . '/' . $extension . '/view' . $path;

        return ($relative == false) ? Self::BASE_URL . $url : $url;
    }

    /**
     * Return true if url is valid
     *
     * @param string $url
     * @return boolean
     */
    public static function isValid(string $url): bool
    {
        return !(\filter_var($url,FILTER_VALIDATE_URL) === false);
    }

    /**
     * Add url schema
     *
     * @param string $url
     * @param string $schema
     * @return string
     */
    public static function addSchema(string $url, string $schema = 'http://'): string 
    {        
        $schema = \parse_url($url,PHP_URL_SCHEME);

        return (empty($schema) == true || $schema === false) ? $schema . $url : $url;
    }
}
