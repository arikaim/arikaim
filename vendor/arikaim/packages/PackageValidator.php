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

use Arikaim\Core\Utils\File;
use Arikaim\Core\Packages\PackageManagerFactory;
use Arikaim\Core\Packages\Composer;
use Arikaim\Core\Utils\Utils;

/**
 * Package validator class
*/
class PackageValidator 
{
    /**
     * Package properties
     *
     * @var array
     */
    protected $requires;

    /**
     * Constructor
     *
     * @param array|null $requires
     */
    public function __construct(?array $requires = []) 
    {
        $this->requires = $requires ?? [];
    }

    /**
     * Validate package requirements
     *
     * @param array|null $requires
     * @return array
     */
    public function validate(?array $requires = null): array
    {
        if (empty($requires) == false) {
            $this->requires = $requires;            
        }

        $result = [
            'core'       => $this->validateCore(),
            'library'    => $this->validateItems('library','library'),
            'extensions' => $this->validateItems('extension','extensions'),
            'modules'    => $this->validateItems('module','modules'),
            'themes'     => $this->validateItems('template','themes'),
            'composer'   => $this->validateComposerPackages(),
            'pip'        => $this->validatePythonPackages()
        ];
        $result['count'] = 
            count($result['library']) + 
            count($result['extensions']) + 
            count($result['modules']) + 
            count($result['themes']) + 
            count($result['pip']) + 
            count($result['composer']); 
        
        return $result;
    }

    /**
     * Parse item name 
     *
     * @param string $name
     * @return array
     */
    protected function parseItemName(string $name): array
    {
        $tokens = \explode(':',$name);
        $version = $tokens[1] ?? null;
        $option = $tokens[2] ?? $version;
        $optinal = ($option == 'optional');
        $version = ($version == 'optional') ? null : $version;
        
        return [$tokens[0],$version,$optinal];
    }

    /**
     * Get validation result item
     *
     * @param string $name
     * @param string|null $requiredVersion
     * @param string|null $packageVersion
     * @param boolean $valid
     * @param boolean $optional
     * @return array
     */
    protected function getResultItem(string $name, ?string $requiredVersion, ?string $packageVersion, bool $valid, bool $optional): array
    {
        $warning = false;
        if (empty($requiredVersion) == false && $valid == true) {
            $valid = Utils::checkVersion($packageVersion,$requiredVersion);
        }         
        if ($optional == true && $valid == false) {
            $warning = true;
            $valid = true;
        } 

        return [
            'name'            => $name,
            'version'         => $requiredVersion,
            'package_version' => $packageVersion,
            'warning'         => $warning,
            'optional'        => $optional,
            'valid'           => $valid
        ];
    }  

    /**
     * Validate composer packages
     *
     * @return array
     */
    public function validateComposerPackages(): array 
    {
        $result = [];
        $items = $this->requires['composer'] ?? [];
        if (count($items) == 0) {
            return [];
        }
        $packageInfo = Composer::getLocalPackagesInfo($items);

        foreach ($items as $item) {
            list($name,$requiredVersion,$optional) = $this->parseItemName($item);
            $valid = (isset($packageInfo[$name]) == true);
            $packageVersion = $packageInfo[$name]['version'] ?? null;
            if (empty($packageVersion) == true) {
                $valid = false;
            }  
             
            $result[] = $this->getResultItem($name,$requiredVersion,$packageVersion,$valid,$optional);
        }
        
        return $result;
    }

    /**
     * Validate python packages
     *
     * @return array
     */
    public function validatePythonPackages(): array 
    {
        $result = [];
        $items = $this->requires['pip'] ?? [];
        if (count($items) == 0) {
            return [];
        }
      
        foreach ($items as $item) {
            list($name,$requiredVersion,$optional) = $this->parseItemName($item);
            $valid = true;
            $packageVersion = $requiredVersion;
            
            $result[] = $this->getResultItem($name,$requiredVersion,$packageVersion,$valid,$optional);
        }
        
        return $result;
    }

    /**
     * Validate required items
     *
     * @param string $packageType
     * @param string $requireItemKey
     * @return array
     */
    protected function validateItems(string $packageType, string $requireItemKey): array
    {
        $items = $this->requires[$requireItemKey] ?? [];
        $result = [];

        foreach ($items as $item) {
            list($name,$requiredVersion,$optional) = $this->parseItemName($item);
            $fileName = PackageManagerFactory::getPackageDescriptorFileName($packageType,$name);
            $valid = (File::exists($fileName) == true);
          
            if ($valid == true) {
                $properties = File::readJsonFile($fileName);
                $packageVersion = $properties['version'] ?? null;             
            } else {              
                $packageVersion = null;
            }
           
            $result[] = $this->getResultItem($name,$requiredVersion,$packageVersion,$valid,$optional);          
        }

        return $result;
    }

    /**
     * Validate required items
     *   
     * @return array
     */
    protected function validateCore(): array
    {
        $result = [];
        $coreItem = $this->requires['core'] ?? false;
        $coreVersion = Composer::getInstalledPackageVersion("arikaim/core");    
        if ($coreItem == false) {
            $result[] = $this->getResultItem('Core',$coreVersion,$coreVersion,true,false);           
        } else{
            list($name,$requiredVersion,$optional) = $this->parseItemName($coreItem);
            $result[] = $this->getResultItem('Core',$requiredVersion,$coreVersion,true,false);        
        }

        return $result;
    }
}
