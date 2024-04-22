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

/**
 * Themes trait
*/
trait Themes 
{
    /**
     * Get default theme
     *
     * @return string|null
     */
    public function getDefautTheme(): ?string
    {
        return $this->properties->get('default-theme',null);
    } 

    /**
     * Get themes
     *
     * @return array
     */
    public function getThemes(): array
    {
        return $this->properties->get('themes',[]);
    }
}
