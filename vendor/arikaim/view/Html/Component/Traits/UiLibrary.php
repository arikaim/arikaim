<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
 */
namespace Arikaim\Core\View\Html\Component\Traits;

use Arikaim\Core\Utils\Path;
use Arikaim\Core\Http\Url;
use Arikaim\Core\Utils\Text;

/**
 * UiLibrary helpers
 */
trait UiLibrary
{
    /**
     * Return library properties
     *
     * @param string $name
     * @param string|null $version
     * @return array
     */
    public function getLibraryProperties(string $name, ?string $version = null): array
    { 
        $fileName = Path::getLibraryPath($name) . 'arikaim-package.json';     
        $data = \json_decode(\file_get_contents($fileName),true);
        $data = ($data === null) ? [] : $data;
        if (empty($version) == true) {       
            return $data;
        }

        $versions = $data['versions'] ?? [];
        $data['files'] = $versions[$version]['files'] ?? [];
        $data['async'] = $versions[$version]['async'] ?? '';
     
        return $data;
    }

    /**
     * Parse library name (name:version)
     *
     * @param string $libraryName
     * @return array
     */
    public function parseLibraryName(string $libraryName): array
    {
        $tokens = \explode(':',$libraryName);
        $version = $tokens[1] ?? null;
        $option = $tokens[2] ?? null;

        return [
            $tokens[0] ?? $libraryName,
            ($version == 'async') ? null : $version,
            (empty($option) == true && $version == 'async') ? 'async' : $option 
        ];
    }

    /**
     * Get library details
     *
     * @param string $libraryName
     * @return array
     */
    public function getLibraryDetails(string $libraryName): array
    {
        list($name,$version,$option) = $this->parseLibraryName($libraryName);
        $properties = $this->getLibraryProperties($name,$version); 
        $params = $this->resolveLibraryParams($properties);           
        $urlParams = (($properties['params-type'] ?? null) == 'url') ? '?' . \http_build_query($params) : '';                  
        $files = [];

        foreach ($properties['files'] as $file) {   
            $libraryFile = Path::getLibraryFilePath($libraryName,$file); 
            $fileType = \pathinfo($libraryFile,PATHINFO_EXTENSION);       
            $fileType = (empty($fileType) == true) ? 'js' : $fileType;
            $files[$fileType][] = [
                'url' => (\filter_var($file,FILTER_VALIDATE_URL) !== false) ? $file . $urlParams : Url::getLibraryFileUrl($name,$file) . $urlParams
            ];               
        }  

        return [
            'files'       => $files,            
            'library'     => $libraryName,
            'async'       => $properties['async'] ?? ($option == 'async'),
            'crossorigin' => $properties['crossorigin'] ?? null
        ];
    }

    /**
     * Get library files
     *
     * @param string $libraryName
     * @param string|null $version
     * @param string|null $option
     * @return array
     */
    public function getLibraryFiles(string $libraryName, ?string $version, ?string $option = null): array
    {       
        $properties = $this->getLibraryProperties($libraryName,$version);          
        $params = $this->resolveLibraryParams($properties);  
        $paramsText = '';
        $urlParams = '';
        $files = [];

        if (count($params) > 0) {
            $urlParams = (($properties['params-type'] ?? null) == 'url') ? '?' . \http_build_query($params) : '';
            \array_walk($params,function (&$value,$key) {
                $value = ' ' . $key . '="' . $value. '"';
            });
            $paramsText = \implode(',',\array_values($params));
        }         
    
        $libraryPath = Path::getLibraryPath($libraryName);

        foreach ($properties['files'] as $file) {
            $type = \pathinfo($libraryPath . $file,PATHINFO_EXTENSION);
            $files[] = [
                'file'        => (\filter_var($file,FILTER_VALIDATE_URL) !== false) ? $file . $urlParams : Url::getLibraryFileUrl($libraryName,$file) . $urlParams,
                'type'        => (empty($type) == true) ? 'js' : $type,
                'params'      => $params,
                'params_text' => $paramsText,
                'library'     => $libraryName,
                'async'       => $properties['async'] ?? ($option == 'async'),
                'crossorigin' => $properties['crossorigin'] ?? null
            ];                      
        }   
        
        return $files;
    }

    /**
     * Resolve library params
     *
     * @param array $properties
     * @return array
     */
    public function resolveLibraryParams(array $properties): array
    {      
        $params = $properties['params'] ?? [];
        $libraryParams = $this->libraryOptions[$properties['name']] ?? ['params' => []];
        $vars = \array_merge([
            'domian'   => DOMAIN,
            'base_url' => BASE_PATH
        ],$libraryParams['params'] ?? []);
            
        return Text::renderMultiple($params,$vars);       
    }
}
