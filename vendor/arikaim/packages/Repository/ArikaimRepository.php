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

use Arikaim\Core\Packages\Interfaces\RepositoryInterface;
use Arikaim\Core\Packages\Repository\Repository;
use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\App\ArikaimStore;
use Exception;

/**
 * Arikaim repository driver class
*/
class ArikaimRepository extends Repository implements RepositoryInterface
{
    /**
     * Get last version url
     *
     * @return string
     */
    public function getLastVersionUrl(): string
    {
        return  ArikaimStore::PACKAGE_VERSION_URL . $this->getPackageName();
    }
    
    /**
     * Get download repo url
     *
     * @param string $version
     * @return string
     */
    public function getDownloadUrl(string $version): string
    {
        return ArikaimStore::PACKAGE_DOWNLOAD_URL;
    }

    /**
     * Return true if repo is private
     *
     * @return boolean
    */
    public function isPrivate(): bool
    {
        return true;
    }

    /**
     * Download package
     *
     * @param string|null $version
     * @return bool
     */
    public function download(?string $version = null): bool
    {
        $version = $version ?? $this->getLastVersion();
        $url = $this->getDownloadUrl($version);
      
        File::setWritable($this->repositoryDir);
        $packageFileName = $this->repositoryDir . $this->getPackageFileName($version); 
      
        if (File::exists($packageFileName) == true) {
            File::delete($packageFileName);   
        }
       
        try {         
            $packageName = $this->getPackageName();

            $this->httpClient->put($url,[
                'sink'        => $packageFileName,
                'form_params' => [
                    'repository'  => $packageName,
                    'license_key' => $this->accessKey
                ]
            ]);
        } catch (Exception $e) { 
            echo $e->getMessage();  
            return false;               
        }
      
        return File::exists($packageFileName);
    }

    /**
     * Get package last version
     *
     * @return string|null
     */
    public function getLastVersion(): ?string
    {       
        $url = $this->getLastVersionUrl();  
        $json = $this->httpClient->fetch($url);
        $data = \json_decode($json,true);

        return (\is_array($data) == true) ? $data['result']['version'] ?? null : null;         
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
        $version = (empty($version) == true) ? $this->getLastVersion() : $version;
        $result = $this->download($version);

        if ($result == true) {
            $repositoryName = $this->getRepositoryName();
            $repositoryFolder = $repositoryName . '-' . $version;
            $repositoryFolder = $this->extractRepository($version,$this->tempDir . $repositoryFolder);
            if ($repositoryFolder == false) {
                // Error extracting zip repository file
                return false;
            }
            $json = $this->storage->read('temp/' . $repositoryFolder . '/arikaim-package.json');
            
            if (Utils::isJson($json) == true) {
                $packageProperties = \json_decode($json,true);
                $packageName = $packageProperties['name'] ?? false;
                if ($packageName != false) {   
                    $sourcePath = $this->tempDir . $repositoryFolder;
                    $destinatinPath = $this->installDir . $packageName;
                    $result = File::copy($sourcePath,$destinatinPath);
                    
                    return $result;
                }
                // Missing package name in arikaim-package.json file.
                return false;
            }
            // Not valid package
            return false;
        }

        // Can't download repository
        return false;
    }
}
