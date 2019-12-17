<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\View;

use Arikaim\Core\View\Template\Template;
use Arikaim\Core\Http\Session;

/**
 * Template theme
 */
class Theme 
{
    /**
     *  Default theme name
     */
    const DEFAULT_THEME_NAME = 'default';

    /**
     * Return current template theme
     *
     * @param string $template_name
     * @param string $defaultTheme
     * @return string
     */
    public static function getCurrentTheme($templateName = null, $defaultTheme = Self::DEFAULT_THEME_NAME)
    {   
        $templateName = ($templateName == null) ? Template::getTemplateName() : $templateName;         
    
        return Session::get("current.theme.$templateName",$defaultTheme);
    }

    /**
     * Set current theme
     *
     * @param string $theme
     * @param string $templateName
     * @return void
     */
    public static function setCurrentTheme($theme, $templateName = null)
    {
        $templateName = (empty($templateName) == true) ? Template::getTemplateName() : $templateName; 

        return Session::set("current.theme.$templateName",$theme);     
    }
}
