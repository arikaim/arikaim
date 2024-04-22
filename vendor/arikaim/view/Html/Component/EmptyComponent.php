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

/**
 * Empty component
 */
class EmptyComponent extends BaseComponent implements HtmlComponentInterface
{
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
            HtmlComponentInterface::EMPTY_COMPONENT_TYPE);
    }

    /**
     * Return true if component is valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * Init component
     *
     * @return void
     */
    public function init(): void 
    {
        parent::init();  
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
        $this->mergeContext($params);
        
        return true;
    }
}
