<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Repository;

use Arikaim\Core\Packages\Composer;
use Arikaim\Core\Packages\Interfaces\RepositoryInterface;
use Arikaim\Core\Packages\Repository\Repository;

/**
 * Composer repository driver class
*/
class ComposerRepository extends Repository implements RepositoryInterface
{
    /**
     * Get last version url
     *
     * @return string
     */
    public function getLastVersionUrl(): string
    {
        $tokens = explode('/',$this->getPackageName());

        return Composer::getPacakgeInfoUrl($tokens[0],$tokens[1]);
    }
    
    /**
     * Get download repo url
     *
     * @param string $version
     * @return string
     */
    public function getDownloadUrl(string $version): string
    {
        $tokens = explode('/',$this->getPackageName());

        return Composer::getPacakgeInfoUrl($tokens[0],$tokens[1]);
    }

    /**
     * Return true if repo is private
     *
     * @return boolean
    */
    public function isPrivate(): bool
    {
        return false;
    }

    /**
     * Download package
     *
     * @param string|null $version
     * @return bool
     */
    public function download(?string $version = null): bool
    {
        return false;
    }

    /**
     * Get package last version
     *
     * @return string|null
     */
    public function getLastVersion(): ?string
    {       
        $tokens = explode('/',$this->getPackageName());
        $version = Composer::getLastVersion($tokens[0],$tokens[1]);      
        
        return ($version === false) ? null : $version;
    }

    /**
     * Resolve package name and repository name
     *
     * @return void
     */
    protected function resolvePackageName(): void
    {
        $tokens = \explode('/',\trim($this->repositoryUrl ?? ''));   

        $this->repositoryName = $tokens[1];
        $this->packageName = $this->repositoryUrl;    
    }

    /**
     * Install package
     *
     * @param string|null $version
     * @return boolean
     */
    public function install(?string $version = null): bool
    {
        Composer::requirePackage($this->getPackageName());        
        $installedPackage = Composer::getInstalledPackageInfo($this->getPackageName());

        return ($installedPackage == null) ? false : true;
    }
}
