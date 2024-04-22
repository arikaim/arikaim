<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Packages;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Arikaim\Core\Utils\Curl;
use Arikaim\Core\Utils\File;

/**
 * Composer commands
 */
class Composer
{   
    /**
     * Run composer commmand
     *
     * @param string $command
     * @param string|array|null $packages
     * @param bool $quiet
     * @return void
     */
    public static function run(string $command, $packages = null, bool $quiet = false): void
    {       
        \putenv('COMPOSER_HOME=' . APP_PATH . '/vendor/bin/composer');
        \putenv('COMPOSER_CACHE_DIR=/dev/null');
      
        $cmd['command'] = $command;
        if (empty($packages) == false) {
            $cmd['packages'] = (\is_string($packages) == true) ? [$packages] : $packages;
        }
        if ($quiet == true) {
            $option = new InputOption('--quiet',null,InputOption::VALUE_NONE,'Quiet');
            $cmd['--quiet'] = $option;
        }
      
        $input = new ArrayInput($cmd);       
        $application = new Application();    
        $application->setAutoExit(false); 

        $application->run($input);
    }

    /**
     * Run update package command
     *
     * @param string $packageName
     * @param boolean $quiet     
     * @return void
     */
    public static function updatePackage(string $packageName, bool $quiet = true)
    {
        return Self::run('update',[$packageName],$quiet);
    }

    /**
     * Run remove package command
     *
     * @param string $packageName
     * @param boolean $quiet     
     * @return void
     */
    public static function removePackage(string $packageName, bool $quiet = true)
    {
        return Self::run('remove',[$packageName],$quiet);
    }

    /**
     * Run require package command
     *
     * @param string $packageName
     * @param boolean $quiet     
     * @return void
     */
    public static function requirePackage(string $packageName, bool $quiet = true)
    {
        return Self::run('require',[$packageName],$quiet);
    }

    /**
     * Get package data
     *
     * @param string $vendor
     * @param string $package
     * @return array|null
     */
    public static function getPackageData(string $vendor, string $package)
    {
        $info = Curl::get(Self::getPacakgeInfoUrl($vendor,$package));

        return (empty($info) == true) ? null : \json_decode($info,true);
    }

    /**
     * Get package info
     *
     * @param string $vendor Package vendor name
     * @param string $package Package name
     * @return array|null
     */
    public static function getPackageInfo(string $vendor, string $package)
    {            
        $info = Curl::get(Self::getPacakgeInfoUrl($vendor,$package));
        $data = \json_decode($info,true);

        return (\is_array($data) == true) ? $data : null;       
    }

    /**
     * Get package last version
     *
     * @param string $vendor
     * @param string $package
     * @return string|false
     */
    public static function getLastVersion(string $vendor, string $package)
    {
        $info = Self::getPackageInfo($vendor,$package);
        $versions = $info['package']['versions'] ?? false;
        if ($versions === false) {
            return false;
        }
        $keys = \array_keys($versions);
       
        return ($keys[0] == 'dev-master') ? $keys[1] : $keys[0];
    }

    /**
     * Get installed package version
     *     
     * @param string $packageName
     * @param string|null $path
     * @return string|false
     */
    public static function getInstalledPackageVersion(string $packageName, ?string $path = null)
    {
        $packages = Self::readInstalledPackages($path);
        if ($packages === false) {
            return false;
        }
        $packages = $packages['packages'] ?? $packages;

        foreach ($packages as $package) {
            if ($package['name'] == $packageName) {
                return $package['version'];
            };   
        }

        return false;
    }

    /**
     * Get installed package info
     *
     * @param string $name
     * @return array|null
     */
    public static function getInstalledPackageInfo(string $name): ?array
    {
        $packages = Self::readInstalledPackages();     
        $packages = $packages['packages'] ?? $packages;

        foreach ($packages as $package) {
            if ($package['name'] == $name) {
                return $package;
            }
        }

        return null;
    }

    /**
     * Get local package info
     *   
     * @param array $packagesList
     * @param string|null $path
     * @return array
     */
    public static function getLocalPackagesInfo(array $packagesList, ?string $path = null)
    {
        $installedPackages = Self::readInstalledPackages($path);     
        $packages = $installedPackages['packages'] ?? $installedPackages;

        foreach ($packagesList as $item) {
            $result[$item]['version'] = null;                  
        }
        
        if (\is_array($packages) == false) {
            return $result;
        }

        foreach ($packages as $package) {
            $key = \array_search($package['name'],$packagesList);

            if ($key !== false) {
                $result[$package['name']]['version'] = $package['version'];
            };   
        }

        return $result;
    }

    /**
     * Return true if composer package is installed
     *   
     * @param string|array $packageList
     * @param string|null $path
     * @return boolean
     */
    public static function isInstalled($packageList, ?string $path = null)
    {
        $packageList = (\is_string($packageList) == true) ? [$packageList] : $packageList;
        
        foreach ($packageList as $package) {          
            if (Self::getInstalledPackageVersion($package,$path) === false) {
                return false;
            }
        }
       
        return true;
    }

    /**
     * Read local packages info file 
     *
     * @param string|null $path
     * @return array|false
     */
    public static function readInstalledPackages(?string $path = null)
    {
        $path = $path ?? ROOT_PATH . BASE_PATH;
        $filePath = $path . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.json';

        return File::readJsonFile($filePath);       
    }   
    
    /**
     * Get package info url
     *
     * @param string $vendor
     * @param string $package
     * @return string
     */
    public static function getPacakgeInfoUrl(string $vendor, string $package): string
    {
        return 'https://packagist.org/packages/' . $vendor . '/' . $package . '.json';
    }
}
