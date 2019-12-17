<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View\Template;

use Arikaim\Core\Http\Session;

/**
 * Html Template
*/
class Template
{
    const SYSTEM_TEMPLATE_NAME = 'system';
    const DEFAULT_TEMPLATE_NAME = 'blog';
    
    /**
     * Return libraries
     *
     * @return array
     */
    public static function getLibraries()    
    {
        return Session::get("ui.included.libraries");
    }

    /**
     * Get template loader component name
     *
     * @return string|null
     */
    public static function getLoader()
    {
        return Session::get("template.loader");
    }

    /**
     * Return UI frameworks
     *
     * @return array
     */
    public static function getFrameworks()    
    {
        return Session::get("ui.included.frameworks");
    }

    /**
     * Return current template name
     *
     * @return void
     */
    public static function getTemplateName()     
    {                            
        return Session::get('current.template',Self::DEFAULT_TEMPLATE_NAME);               
    }

    /**
     * Set current template name
     *
     * @return void
     */
    public static function setTemplateName($name)     
    {                            
        return Session::set('current.template',$name);               
    }

    /**
     * Set current front end framework.
     *
     * @param string $library UI library name
     * @return void
     */
    public static function setCurrentFramework($library)
    {
        Session::set("current.framework",$library);
    }

    /**
     * Return current front end framework used in page
     *
     * @return string
     */
    public static function getCurrentFramework()
    {
        $framework = Session::get("current.framework");
        if (empty($framework) == true || $framework == null) {
            $frameworks = json_decode(Self::getFrameworks());
            $frameworks = (is_array($frameworks) == true) ? $frameworks : [];
            $framework = end($frameworks);
            Self::setCurrentFramework($framework);
        }

        return $framework;
    }

    /**
     * Get macro path
     *
     * @param string $macroName
     * @param string $template
     * @return string
     */
    public static function getMacroPath($macroName, $template = null)
    {
        $template = (empty($template) == true) ? Self::getTemplateName() : $template; 

        return DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . "macros" . DIRECTORY_SEPARATOR . $macroName;
    }

    /**
     * Get system macro path
     *
     * @param string $macroName
     * @return string
     */
    public static function getSystemMacroPath($macroName)
    {
        return Self::getMacroPath($macroName,Self::SYSTEM_TEMPLATE_NAME);
    }
}
