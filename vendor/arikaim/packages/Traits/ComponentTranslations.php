<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Traits;

use Arikaim\Core\Utils\File;
use Arikaim\Core\Utils\Utils;
use Arikaim\Core\Utils\Path;
use Arikaim\Core\Collection\Arrays;

/**
 * View component translations trait
*/
trait ComponentTranslations 
{
    /**
     * Skip component option files
     *
     * @var array
     */
    private $skipFiles = [
        'component.json',
        'page.json'
    ];

    /**
     * Get package view components translations
     *
     * @param string $componentName
     * @param string $type
     * @return array
     */
    public function getComponentTranslations(string $componentName, string $type = 'components'): array
    {                       
        $path = $this->getComponentPath($componentName,$type);
       
        $files = [];
        $languages = [];       
        if (File::exists($path) == true) {
            $files = File::scanDir($path);
            $languages = $this->getComponentLanguages($files);
        }

        $result['name'] = $componentName;
        $result['path'] = $path;
        $result['base_name'] = File::baseName($path);
        $result['files'] = $files;
        $result['languages'] = $languages;

        return $result;
    }

    /**
     * Read language translation 
     *
     * @param string $componentName
     * @param string $language
     * @param string $type
     * @return array|false
    */
    public function readTranslation(string $componentName, ?string $language, string $type = 'components') 
    {        
        $fileName = $this->getTranslationFileName($componentName,$language,$type);

        return ($fileName === false) ? false : File::readJsonFile($fileName);        
    }

    /**
     * Get translation relative file name path
     *
     * @param string $componentName
     * @param string $language
     * @param string $type
     * @return string
    */
    public function getTranslationRelativeFileName(string $componentName, ?string $language, string $type = 'components'): string
    {
        $filePath = $this->getTranslationFileName($componentName, $language, $type);

        return Path::getRelativePath($filePath);
    }

    /**
     * Get translation file name
     *
     * @param string $componentName
     * @param string $language
     * @param string $type
     * @return string
     */
    public function getTranslationFileName(string $componentName, ?string $language, string $type = 'components')
    {
        $translations = $this->getComponentTranslations($componentName,$type);
        if ($translations === false) {          
            return false;
        }
        if ($this->hasLanguage($translations,$language) == false) {
            return false;
        }

        return $this->resolveTranslationFileName($translations['path'],$language);           
    }

    /**
     * Save translation
     *
     * @param array $data
     * @param string $componentName
     * @param string $language
     * @param string $type
     * @return boolean
     */
    public function saveTranslation($data, string $componentName, ?string $language, string $type = 'components'): bool
    {
        $fileName = $this->getTranslationFileName($componentName,$language,$type);
        if ($fileName === false) {
            return false;
        }
        if (File::isWritable($fileName) == false) {
            if (File::setWritable($fileName) == false) {
                return false;
            }
        }
        $jsonText = Utils::jsonEncode($data);
       
        return File::write($fileName,$jsonText); 
    }

    /**
     * Get translation property value
     *
     * @param array|string $data
     * @param string $key
     * @param string $separator
     * @param string|null $language
     * @param string $type
     * @return mixed
     */
    public function readTranlationProperty($data, string $key, string $separator = '_', ?string $language = null, string $type = 'components')
    {
        if (\is_string($data) == true) {
            $data = $this->readTranslation($data,$language,$type);
            if ($data === false) {
                return null;
            }
        }

        return Arrays::getValue($data,$key,$separator);
    }

    /**
     * Set translation property value
     *
     * @param array $data
     * @param string $key
     * @param mixed $value
     * @param string $separator
     * @return array
     */
    public function setTranslationProperty(array $data, string $key, $value, string $separator = '_')
    {
        return Arrays::setValue($data,$key,$value,$separator);       
    }

    /**
     * Resolve translation file name
     *
     * @param string $path
     * @param string $language
     * @return string
     */
    public function resolveTranslationFileName(string $path, ?string $language): string
    {
        $baseName = File::baseName($path);
        $fileName = ($language == 'en') ? $baseName . '.json' : $baseName . '-' . $language . '.json';

        return $path . DIRECTORY_SEPARATOR . $fileName; 
    }

    /**
     * Return true if component have language 
     *
     * @param array $translations
     * @param string $language
     * @return boolean
     */
    public function hasLanguage($translations, ?string $language): bool
    {
        $translations = (\is_array($translations) == false) ? [] : $translations;

        return \in_array($language,$translations['languages']);
    } 
    
    /**
     * Get component languages
     *
     * @param array $componentFiles
     * @return array
     */
    public function getComponentLanguages(array $componentFiles): array
    {
        $result = [];
        foreach ($componentFiles as $file) {
            if (\in_array($file,$this->skipFiles) == true) {
                // skip component options file
                continue;
            }
           if (File::getExtension($file) == 'json') { 
                $tokens = \explode('-',\str_replace('.json','',$file));
                $languageCode = (isset($tokens[1]) == false) ? 'en' : \end($tokens);  
                $result[] = (\strlen($languageCode) == 2) ? $languageCode : 'en';                                        
           }
        }

        return \array_unique($result);
    }
}
