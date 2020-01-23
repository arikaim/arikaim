<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error\Renderer;

use Arikaim\Core\System\Error\ErrorRendererInterface;
use Arikaim\Core\Interfaces\SystemErrorInterface;

/**
 * Render error
 */
class HtmlPageErrorRenderer implements ErrorRendererInterface
{
    /**
     * Page reference
     *
     * @var SystemErrorInterface
     */
    protected $error;

    /**
     * Constructor
     *
     * @param Page $page
     * @return void
     */
    public function __construct(SystemErrorInterface $error)
    {
        $this->error = $error;
    }

    /**
     * Render error
     *
     * @param array $errorDetails
     * @return string
     */
    public function render($errorDetails)
    {                  
        try {   
            switch($errorDetails['base_class']) {
                case 'HttpNotFoundException': {                   
                    $output = $this->error->renderPageNotFound(['error' => $errorDetails])->getHtmlCode();
                    break;
                }
                default: {                   
                    $output = $this->error->renderApplicationError(['error' => $errorDetails])->getHtmlCode();            
                }
            }
        } catch(\Exception $exception) {           
            $output = $this->error->renderApplicationError(['error' => $errorDetails])->getHtmlCode();  
        }

        return $output;        
    }
}
