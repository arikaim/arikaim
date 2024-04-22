<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
 */
namespace Arikaim\Core\View\Traits;

/**
 * Theme global vars
 */
trait ThemeGlobals
{    
    /**
     * Gte template theme file name
     *
     * @param string $themeName
     * @return string
     */
    public function getTemplateThemeFile(string $themeName): string
    {
        return $this->templatesPath . $this->primaryTemplate . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $themeName . '.json';
    } 

    /**
     * Include theme global vars
     *
     * @param string|null $themeName
     * @return boolean
     */
    protected function includeThemeGlobals(?string $themeName = null): bool
    { 
        if (empty($themeName) == true) {
            return false;
        }
        $themeGlobals = $this->cache->fetch('template.theme.' . $this->primaryTemplate . '.' . $themeName);
        
        if ($themeGlobals === false) {
            $fileName = $this->getTemplateThemeFile($themeName);
            if (\is_file($fileName) == false) {
                return false;
            }          
            $themeGlobals = \json_decode(\file_get_contents($fileName),true);   
            if ($themeGlobals == null) {
                return false;
            } 
            $this->cache->save('template.theme.' . $this->primaryTemplate . '.' . $themeName,$themeGlobals);
        }
       
        $this->addGlobal('theme',$themeGlobals);
        
        return true;
    }
}
