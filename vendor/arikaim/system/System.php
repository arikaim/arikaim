<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System;

use Arikaim\Core\Utils\Utils;

/**
 * Core system helper class
 */
class System 
{
    const UNKNOWN = 1;
    const WINDOWS = 2;
    const LINUX   = 3;
    const OSX     = 4;

    /**
     * Call static methods from instance
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return Utils::callStatic(System::class,$name,$arguments);
    }

    /**
     * Get system info
     *
     * @return array
     */
    public static function getSystemInfo() 
    {  
        $os = posix_uname();   
        
        return [
            'php_version' => Self::getPhpVersion(),       
            'os_name'     => explode(' ',$os['sysname'])[0],
            'os_version'  => $os['release'],
            'os_machine'  => $os['machine'],
            'os_node'     => $os['nodename']
        ];       
    }

    /**
     * Set script execution tile limit (0 - unlimited)
     *
     * @param integer $time
     * @return void
     */
    public static function setTimeLimit($time)
    {
        set_time_limit($time);       
    }

    /**
     * Return php version
     *
     * @return string
     */
    public static function getPhpVersion()
    {                   
        return substr(phpversion(),0,6);
    }
   
    /**
     * Return php extensions list
     *
     * @return array
     */
    public function getPhpExtensions()
    {
        $data = [];
        $items = get_loaded_extensions(false);
        foreach ($items as $item) {
            $version = Utils::formatVersion(Self::getPhpExtensionVersion($item));   
            array_push($data,['name' => $item,'version' => $version]);
        }

        return $data;
    }

    /**
     * Return php extension version
     *
     * @param string $phpExtensionName
     * @return string
     */
    public static function getPhpExtensionVersion($phpExtensionName)
    {
        $ext = new \ReflectionExtension($phpExtensionName);

        return substr($ext->getVersion(),0,6);
    }

    /**
     * Return true if php extension is instaed
     *
     * @param string $phpExtensionName
     * @return boolean
     */
    public static function hasPhpExtension($phpExtensionName) 
    {
        return extension_loaded($phpExtensionName);
    }

    /**
     * Return true if PDO driver is installed
     *
     * @param string $driverName
     * @return boolean
     */
    public static function hasPdoDriver($driverName)
    {
        $drivers = Self::getPdoDrivers();

        return (is_array($drivers) == true) ? in_array($driverName,$drivers) : false;        
    }

    /**
     * Return PDO drivers list
     *
     * @return array
     */
    public static function getPdoDrivers()
    {
        return (Self::hasPhpExtension('PDO') == false) ? [] : \PDO::getAvailableDrivers();
    }

    /**
     * Return Stream wrappers
     *
     * @return array
     */
    public static function getStreamWrappers()
    {
        return stream_get_wrappers();
    }

    /**
     * Return true if stream wrapper are installed
     *
     * @param string $protocol
     * @return boolean
     */
    public static function hasStreamWrapper($protocol)
    {      
        return in_array($protocol,Self::getStreamWrappers());
    }

    /**
     * Get debug backtrace
     *
     * @return array
     */
    public static function getBacktrace()
    {
        return debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    }

    /**
     * Return true if script is run in console
     *
     * @return boolean
     */
    public static function isConsole()
    {
        return (php_sapi_name() == "cli") ? true : false;          
    }   

    /**
     * Output text
     *
     * @param string $text
     * @param string $eof
     * @return void
     */
    public static function writeLine($text, $eof = null)
    {       
        echo $text . "\n";
    }

    /**
     * Return OS
     *
     * @return integer
     */
    public static function getOS() 
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'): {
                return Self::OSX;
            }
            case stristr(PHP_OS, 'WIN'): {
                return Self::WINDOWS;
            }
            case stristr(PHP_OS, 'LINUX'): {
                return Self::LINUX;
            }
            default: {
                return Self::UNKNOWN;
            }
        }
    }
 
    /**
     * Get default output
     *
     * @return string
     */
    public static function getDefaultOutput()
    {
        return (DIRECTORY_SEPARATOR == '\\') ? 'NUL' : '/dev/null';
    }
}
