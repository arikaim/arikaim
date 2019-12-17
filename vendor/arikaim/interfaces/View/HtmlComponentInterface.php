<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\View;

/**
 * Extension interface
 */
interface HtmlComponentInterface 
{  
    /**
     * Render html component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @return ComponentInterface
    */
    public function render($name, $params = [], $language = null);
}
