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
use Arikaim\Core\System\NodeJs;
use Arikaim\Core\System\Process;
use Arikaim\Core\Packages\Composer;

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
     * Get apache current user
     * @return string 
     */
    public static function getApacheUser(): string
    {
        return exec('whoami');
    }

    /**
     * Get nodejs version
     *
     * @return string:null
     */
    public static function getNodeJsVersion(): ?string 
    {
        return NodeJs::getVersion();
    }

    /**
     * Get registered socket transports
     *
     * @return array
     */
    public static function getStreamTransports()
    {
        return \stream_get_transports();
    } 

    /**
     * Get php console ver
     *
     * @return mixed
     */
    public static function getPhpConsoleVersion()
    {
        $php = (Process::findPhp() === false) ? 'php' : Process::findPhp();
        $command = $php . ' --version';

        return Process::run($command);
    }

    /**
     * Get system info
     *
     * @return array
     */
    public function getInfo(): array
    {
        return Self::getSystemInfo();
    }

    /**
     * Get system info
     *
     * @return array
     */
    public static function getSystemInfo(): array 
    {  
        $os = \posix_uname();   

        return [
            'core'            => Composer::getInstalledPackageVersion('arikaim/core'),
            'php_version'     => Self::getPhpVersion(),       
            'os_name'         => \explode(' ',$os['sysname'])[0],
            'os_version'      => $os['release'],
            'os_machine'      => $os['machine'],
            'apache_version'  => Self::getApacheVersion(),
            'apache_modules'  => Self::getApacheModules(),
            'current_user'    => \get_current_user(),           
            'user_id'         => \getmyuid(), 
            'os_node'         => $os['nodename']
        ];       
    }

    /**
     * Get apache version
     *
     * @return string|null
    */
    public static function getApacheVersion(): ?string
    {
        return (\function_exists('apache_get_version') == true) ? \apache_get_version() : null;
    }

    /**
     * Get apache modules
     *
     * @return array
     */
    public static function getApacheModules(): array
    {
        return (\function_exists('apache_get_modules') == true) ? \apache_get_modules() : [];
    }

    /**
     * Set script execution tile limit (0 - unlimited)
     *
     * @param integer $time
     * @return void
     */
    public static function setTimeLimit($time): void
    {
        \set_time_limit($time);       
    }

    /**
     * Return php version
     *
     * @return string
     */
    public static function getPhpVersion(): string
    {                   
        return \substr(\phpversion(),0,6);
    }
   
    /**
     * Return php extensions list
     *
     * @return array
     */
    public function getPhpExtensions(): array
    {
        $data = [];
        $items = \get_loaded_extensions(false);
        
        foreach ($items as $item) {
            $version = Utils::formatVersion(Self::getPhpExtensionVersion($item));   
            $data[] = [
                'name'    => $item,
                'version' => $version
            ];
        }

        return $data;
    }

    /**
     * Return php extension version
     *
     * @param string $phpExtensionName
     * @return string|null
     */
    public static function getPhpExtensionVersion(string $phpExtensionName): ?string
    {
        $ext = new \ReflectionExtension($phpExtensionName);

        return \substr($ext->getVersion(),0,6);
    }

    /**
     * Return true if php extension is instaed
     *
     * @param string $phpExtensionName
     * @return boolean
     */
    public static function hasPhpExtension(string $phpExtensionName) 
    {
        return \extension_loaded($phpExtensionName);
    }

    /**
     * Return true if PDO driver is installed
     *
     * @param string $driverName
     * @return boolean
     */
    public static function hasPdoDriver(string $driverName): bool
    {
        $drivers = Self::getPdoDrivers();

        return (\is_array($drivers) == true) ? \in_array($driverName,$drivers) : false;        
    }

    /**
     * Get opcache status
     *
     * @return array
     */
    public static function getOpcache(): array
    {
        return [
            'config' => \opcache_get_configuration(),
            'status' => \opcache_get_status()
        ];
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
    public static function getStreamWrappers(): array
    {
        return \stream_get_wrappers();
    }

    /**
     * Return true if stream wrapper are installed
     *
     * @param string $protocol
     * @return boolean
     */
    public static function hasStreamWrapper(string $protocol): bool
    {      
        return \in_array($protocol,Self::getStreamWrappers());
    }

    /**
     * Get debug backtrace
     *
     * @return array
     */
    public static function getBacktrace()
    {
        return \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    }

    /**
     * Return true if script is run in console
     *
     * @return boolean
     */
    public static function isConsole(): bool
    {
        return (\php_sapi_name() == 'cli');    
    }   

    /**
     * Output text
     *
     * @param string|null $text
     * @param string|null $eof
     * @return void
     */
    public static function writeLine(?string $text, $eof = null): void
    {       
        $eof = $eof ?? "\n";
        $text = $text ?? '';

        echo $text . $eof;
    }

    /**
     * Return OS
     *
     * @return integer
     */
    public static function getOS(): int 
    {
        switch (true) {
            case \stristr(PHP_OS,'DAR'): {
                return Self::OSX;
            }
            case \stristr(PHP_OS,'WIN'): {
                return Self::WINDOWS;
            }
            case \stristr(PHP_OS,'LINUX'): {
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
    public static function getDefaultOutput(): string
    {
        return (DIRECTORY_SEPARATOR == '\\') ? 'NUL' : '/dev/null';
    }
}
