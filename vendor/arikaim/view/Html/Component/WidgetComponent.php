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
use Arikaim\Core\Interfaces\View\RequireAccessInterface;

use Arikaim\Core\View\Html\Component\Traits\IncludeOption;
use Arikaim\Core\View\Html\Component\Traits\Options;
use Arikaim\Core\View\Html\Component\Traits\Properties;

/**
 * Widget html component
 */
class WidgetComponent extends BaseComponent implements HtmlComponentInterface, RequireAccessInterface
{
    use 
        Options,
        Properties,
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
    public function __construct(
        string $name,
        string $language,
        string $viewPath,
        string $extensionsPath,
        string $primaryTemplate
    ) 
    {
        parent::__construct(
            $name,'components',
            $language,
            $viewPath,
            $extensionsPath,
            $primaryTemplate,
            HtmlComponentInterface::WIWGET_COMPONENT_TYPE
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
        $this->resolveHtmlContent();
        // options
        $this->processIncludeOption();   
        $this->processStylesOption(); 
        $this->processDataOption();
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
        $this->addComponentFile('css');    

        if ($this->isValid() == false) {           
            return false;                
        }
        
        $this->mergeProperties();     
        // merge params context
        $this->mergeContext($params);  
        // merge styles json file
        $this->mergeStyles();   
        // merge data json file
        $this->mergeData(); 

        return true;
    }
}
