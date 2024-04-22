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

use Arikaim\Core\Interfaces\View\HtmlComponentInterface;

/**
 * Extension interface
 */
interface EmailViewInterface extends HtmlComponentInterface
{  
    /**
     * Render email component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language    
     * @return \Arikaim\Core\Interfaces\View\EmailViewInterface
    */
    public function render(string $name, array $params = [], ?string $language = null); 

    /**
     * Get email subject
     *
     * @return string
     */
    public function getSubject(): string;
}
