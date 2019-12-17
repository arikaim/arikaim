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

use Arikaim\Core\System\Composer;

/**
 * Update package
 */
class Update 
{
    /**
     * Composer package name
     *
     * @var string
     */
    protected $packageName;

    /**
     * Constructor
     *
     * @param string $packageName
     */
    public function __construct($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * Update package
     *
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public function update($async = false, $realTimeOutput = false)
    {      
        $output = Composer::updatePackage($this->packageName,$async,$realTimeOutput);  

        return $output;
    }

    /**
     * Return package info
     *
     * @return string
     */
    public function getCoreInfo()
    {
        return Composer::runCommand('show ' . $this->packageName);
    }

    /**
     * Return array with code packages
     *
     * @param integer $resultLength Result maximum lenght
     * @return array
     */
    public function getCorePackagesList($resultLength = null)
    {
        $packageInfo = Composer::getPackageInfo("arikaim","core");
        $list = $packageInfo['package']['versions'];
        unset($list['dev-master']);
        $packages = [];
        $count = 0;       
        
        foreach ($list as $package) {          
            $info['version'] = $package['version'];
            $info['name'] = $package['name'];
            array_push($packages,$info);
            $count++;
            if (($resultLength != null) && ($count >= $resultLength)) {               
                return $packages;
            }
        }

        return $packages;
    }

    /**
     * Get last version
     *
     * @return string
     */
    public function getLastVersion()
    {
        $tokens = explode('/',$this->packageName);
        
        return Composer::getLastVersion($tokens[0],$tokens[1]); 
    }

    /**
     * Get current installed package version
     *
     * @param string|null $path  App path
     * @return string|false
     */
    public function getCurrentVersion($path = null)
    {
        $path = (empty($path) == true) ? ROOT_PATH . BASE_PATH : $path;

        return Composer::getInstalledPackageVersion($path,$this->packageName); 
    } 
}
