<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     View
*/
namespace Arikaim\Core\View\Html\Component;

use Arikaim\Core\View\Html\Component\BaseComponent;
use Arikaim\Core\Interfaces\View\HtmlComponentInterface;

use Arikaim\Core\View\Html\Component\Traits\IncludeOption;
use Arikaim\Core\View\Html\Component\Traits\Options;
use Arikaim\Core\View\Html\Component\Traits\Properties;
use Arikaim\Core\View\Html\Component\Traits\ComponentEditor;

/**
 * Static html component
 */
class StaticHtmlComponent extends BaseComponent implements HtmlComponentInterface
{
    use 
        Properties,
        Options,
        ComponentEditor,
        IncludeOption;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $language  
     * @param string $viewPath
     * @param string $extensionsPath
     * @param string $primaryTemplate
     */
    public function __construct(string $name, string $language, string $viewPath, string $extensionsPath, string $primaryTemplate) 
    {
        parent::__construct(
            $name,
            'components',
            $language,
            $viewPath,
            $extensionsPath,
            $primaryTemplate,
            HtmlComponentInterface::STATIC_COMPONENT_TYPE
        );
    }

    /**
     * Return true if component is valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return ($this->hasContent() == true || $this->hasFiles('js'));
    }

    /**
     * Init component
     *
     * @return void
     */
    public function init(): void 
    {
        parent::init();

        $this->loadProperties();
        $this->loadOptions();       
        $this->hasHtmlContent = true;
        // options
        $this->processIncludeOption();      
        
        if ($this->renderMode == 1) {
            // edit mode
            $this->loadEditorOptions();                    
        }          
    }

    /**
     * Render component data
     *     
     * @param array $params   
     * @return bool
     */
    public function resolve(array $params = []): bool
    {    
        parent::resolve($params);
        $this->addComponentFile('js');   
        
        if ($this->isValid() == false) {                      
            return false;                
        }

        $this->mergeProperties();      
        $this->mergeContext($params);
     
        return true;
    }
}
