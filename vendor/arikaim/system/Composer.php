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

use Arikaim\Core\System\Process;
use Arikaim\Core\Utils\Curl;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Utils\File;

/**
 * Composer commands
 */
class Composer
{   
    /**
     * Run require command
     *
     * @param string $packageName
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function requireCommand($packageName, $async = false, $realTimeOutput = false)
    {
        return Self::runCommand("require $packageName",$async,$realTimeOutput); 
    }
    
    /**
     * Check if package is installed
     *
     * @param string $packageName
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return boolean
     */
    public static function hasPackage($packageName, $async = false, $realTimeOutput = false)
    {
        return Self::runCommand("show $packageName",$async,$realTimeOutput); 
    }

    /**
     * Run show command
     *
     * @param string $packageName
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function show($packageName, $async = false, $realTimeOutput = false)
    {
        return Self::runCommand("show $packageName",$async,$realTimeOutput); 
    }

    /**
     * Run remove comand
     *
     * @param string $packageName
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function remove($packageName,$async = false, $realTimeOutput = false)
    {
        return Self::runCommand("remove $packageName --no-dev",$async,$realTimeOutput); 
    }

    /**
     * Run update package command
     *
     * @param string $packageName
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function updatePackage($packageName, $async = false, $realTimeOutput = false)
    {
        return Self::runCommand("update $packageName --no-dev",$async,$realTimeOutput);
    }

    /**
     * Run update command
     *
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function update($async = false, $realTimeOutput = false)
    {
        return Self::runCommand('update --no-dev',$async,$realTimeOutput);
    }

    /**
     * Run composer command
     *
     * @param string $command
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function runCommand($command, $async = false, $realTimeOutput = false)
    {
        $command = "php " . Path::BIN_PATH . 'composer.phar ' . $command;
        $env = [
            'COMPOSER_HOME'      => Path::BIN_PATH,
            'COMPOSER_CACHE_DIR' => '/dev/null'
        ];

        $process = Process::create($command,$env);
        try {
            if ($async == true) {
                $process->start();
            } else {
                if ($realTimeOutput == true) {
                    $process->run(function ($type, $buffer) {                       
                        echo $buffer;                        
                    });
                }
                $process->run();
            }
            $output = $process->getOutput();
        } catch(\Exception $e) {            
            return $e->getMessage();
        }

        return $output;
    }

    /**
     * Get package data
     *
     * @param string $vendor
     * @param string $package
     * @return array
     */
    public static function getPackageData($vendor, $package)
    {
        $info = Curl::get("http://repo.packagist.org/p/$vendor/$package.json");

        return (empty($info) == true) ? null : json_decode($info,true);
    }

    /**
     * Get package info
     *
     * @param string $vendor Package vendor name
     * @param string $package Package name
     * @return array
     */
    public static function getPackageInfo($vendor, $package)
    {             
        $info = Curl::get("http://packagist.org/packages/$vendor/$package.json");

        return (empty($info) == true) ? null : json_decode($info,true);
    }

    /**
     * Get package last version
     *
     * @param string $vendor
     * @param string $package
     * @return string|false
     */
    public static function getLastVersion($vendor, $package)
    {
        $info = Self::getPackageData($vendor,$package);

        if (is_array($info) == true) {
            $package = $info['packages']["$vendor/$package"];
            $count = (is_array($package) == true) ? (count($package) - 2) : 0;           
            $versions = array_keys($package);
        
            return (isset($versions[$count]) == true) ? $versions[$count] : false;
        }

        return false;
    }

    /**
     * Get installed package version
     *
     * @param string $path
     * @param string $packageName
     * @return string|false
     */
    public static function getInstalledPackageVersion($path, $packageName)
    {
        $filePath = $path . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.json';
        $packages = File::readJsonFile($filePath);
        if ($packages === false) {
            return false;
        }

        foreach ($packages as $package) {
            if ($package['name'] == $packageName) {
                return $package['version'];
            };   
        }

        return false;
    }
}
