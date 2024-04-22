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
interface HtmlPageInterface extends HtmlComponentInterface
{  
    /**
     * Render html component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language    
     * @return \Arikaim\Core\Interfaces\View\ComponentInterface
    */
    public function render(string $name, array $params = [], ?string $language = null); 

    /**
     * Render application error
     *
     * @param array $data
     * @param string|null $language    
     * @param string|null $templateName    
     * @return \Arikaim\Core\Interfaces\View\ComponentInterface
     */
    public function renderApplicationError(array $data = [], ?string $language = null, ?string $templateName = null);

    /**
     * Render system error(s)
     *
     * @param array $data
     * @param string|null $language  
     * @param string|null $templateName    
     * @return \Arikaim\Core\Interfaces\View\ComponentInterface
     */
    public function renderSystemError(array $data = [], ?string $language = null, ?string $templateName = null);

    /**
     * Render page not found 
     *
     * @param array $data
     * @param string|null $language 
     * @param string|null $templateName     
     * @return \Arikaim\Core\Interfaces\View\ComponentInterface
    */
    public function renderPageNotFound(array $data = [], ?string $language = null, ?string $templateName = null);

    /**
     * Get current template name
     *
     * @return string|null
     */
    public function getCurrentTemplate();
    
    /**
     * Render html component
     *
     * @param string $name
     * @param array $params
     * @param string|null $language
     * @param string|null $type
     * @param array $parent
     * 
     * @return Arikaim\Core\Interfaces\View\HtmlComponentInterface;
    */
    public function renderHtmlComponent(
        string $name, 
        array $params = [], 
        ?string $language = null, 
        ?string $type = null,
        array $parent = []
    );
    
    /**
     * Get head properties
     *
     * @return PageHead
     */
    public function head();

    /**
     * Get component files
     *
     * @return array
     */
    public function getComponentsFiles(): array;
}
