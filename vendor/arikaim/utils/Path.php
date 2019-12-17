<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Utils;

/**
 * All path constants and helpers
 */
class Path 
{
    const VIEW_PATH               = APP_PATH . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
    const BIN_PATH                = APP_PATH . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR;
    const EXTENSIONS_PATH         = APP_PATH . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR;  
    const MODULES_PATH            = APP_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
    const CONFIG_PATH             = APP_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
    const CACHE_PATH              = APP_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    const LOGS_PATH               = APP_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    const STORAGE_PATH            = APP_PATH . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR;
    const LIBRARY_PATH            = Self::VIEW_PATH . 'library' . DIRECTORY_SEPARATOR;
    const TEMPLATES_PATH          = Self::VIEW_PATH . 'templates' . DIRECTORY_SEPARATOR;
    const COMPONENTS_PATH         = Self::VIEW_PATH . 'components' . DIRECTORY_SEPARATOR;
    const VIEW_CACHE_PATH         = Self::CACHE_PATH . 'views' . DIRECTORY_SEPARATOR;
    const STORAGE_TEMP_PATH       = Self::STORAGE_PATH . 'temp' . DIRECTORY_SEPARATOR;
    const STORAGE_BACKUP_PATH     = Self::STORAGE_PATH . 'backup' . DIRECTORY_SEPARATOR;
    const STORAGE_REPOSITORY_PATH = Self::STORAGE_PATH . 'repository' . DIRECTORY_SEPARATOR;

    /**
     * Set app path
     *
     * @param string $path
     * @return void
     */
    public static function setAppPath($path)
    {
        if (defined('APP_PATH') == false) {
            define('APP_PATH',ROOT_PATH . BASE_PATH . DIRECTORY_SEPARATOR . $path);  
        }
    }

    /**
     * Get module path
     *
     * @param string $name
     * @return string
     */
    public static function getModulePath($name)
    {
        return Self::MODULES_PATH . $name . DIRECTORY_SEPARATOR;
    }

    /**
     * Get library themes path
     *
     * @param string $library   
     * @return string
     */
    public static function getLibraryThemesPath($library)
    {
        return Self::getLibraryPath($library) . "themes";
    }

    /**
     * Get library file path
     *
     * @param string $library
     * @param string $fileName
     * @return string
     */
    public static function getLibraryFilePath($library, $fileName) 
    {
        return Self::getLibraryPath($library) . $fileName;
    }
    
    /**
     * Get library path
     *
     * @param string $library
     * @return string
     */
    public static function getLibraryPath($library)
    {
        return Self::LIBRARY_PATH . $library . DIRECTORY_SEPARATOR;
    }

    /**
     * Get extension macros relative path
     *
     * @param string $extension
     * @return string
     */
    public static function getExtensionMacrosRelativePath($extension)
    {
        return $extension . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR . "macros" . DIRECTORY_SEPARATOR;
    }

    
    /**
     * Return current script path
     *
     * @return string
     */
    public static function getScriptPath()
    {
        return realpath(dirname(__FILE__));
    }
}
