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

use Arikaim\Core\Packages\Package;
use Arikaim\Core\Packages\Interfaces\PackageInterface;
use Arikaim\Core\System\Traits\PhpConfigFile;
use Arikaim\Core\Utils\Path;

/**
 * UI Library Package class
*/
class LibraryPackage extends Package implements PackageInterface
{ 
    use PhpConfigFile;

    /**
     *  Library params config file
     */
    const LIBRARY_PARAMS_FILE_NAME = Path::CONFIG_PATH . 'ui-library.php';

    /**
     * Get library params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->properties->get('params',[]);
    }

    /**
     * Get theme file (DEPRECATED)
     *
     * @param string $theme  
     * @return string
     */
    public function getThemeFile($theme): string
    {
        return $this->properties->getByPath('themes/' . $theme . '/file','');
    }

    /**
     * Disable library
     *
     * @return bool
     */
    public function disable(): bool
    {
        $this->setStatus(false);

        return true;
    } 

    /**
     * Enable library
     *
     * @return void
     */
    public function enable(): bool
    {
        $this->setStatus(true);

        return true; 
    } 

    /**
     * Set library status (enabled, disbled)
     *
     * @param bool $status
     * @return void
     */
    public function setStatus(bool $status): void
    {
        $libraryConfig = $this->includePhpArray(Self::LIBRARY_PARAMS_FILE_NAME);       
        $name = $this->getName();
        $library = $libraryConfig[$name] ?? [];
        $library['disabled'] = !$status;

        $libraryConfig[$name] = $library;
        
        $this->saveConfigFile(Self::LIBRARY_PARAMS_FILE_NAME,$libraryConfig);
    }

    /**
     * Get library params
     *
     * @return array
     */
    public function getLibraryParams(): array
    {
        $libraryConfig = $this->includePhpArray(Self::LIBRARY_PARAMS_FILE_NAME);       
        $name = $this->getName();

        return $libraryConfig[$name] ?? [];
    }

    /**
     * Save ui library params
     *
     * @param array $params
     * @return boolean
     */
    public function saveLibraryParams(array $params): bool
    {
        $libraryConfig = $this->includePhpArray(Self::LIBRARY_PARAMS_FILE_NAME);       
        $name = $this->getName();
        $library = $libraryConfig[$name]['params'] ?? [];
       
        foreach($params as $item) {
            $key = $item['name'] ?? null;
            $value = $item['value'] ?? null;
            if (empty($key) == false) {
                $library[$key] = $value;
            }          
        }
        $libraryConfig[$name]['params'] = $library;
       
        return $this->saveConfigFile(Self::LIBRARY_PARAMS_FILE_NAME,$libraryConfig);
    }
}
