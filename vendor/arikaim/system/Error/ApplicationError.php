<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error;

use Arikaim\Core\System\Error\ErrorHandlerInterface;

use Arikaim\Core\System\Error\PhpError;
use Arikaim\Core\Http\ApiResponse;
use Arikaim\Core\Interfaces\View\HtmlPageInterface;
use Throwable;

/**
 * Application error handler
 */
class ApplicationError implements ErrorHandlerInterface
{  
    /**
     * Page reference
     *
     * @var HtmlPageInterface
     */
    protected $page;

    /**
     * Constructor
     *
     * @param HtmlPageInterface $page     
     */
    public function __construct(HtmlPageInterface $page)
    {
        $this->page = $page;       
    }

    /**
     * Render error
     *
     * @param Throwable  $exception The caught Throwable object
     * @param string $renderType   
     * @return string   
     */
    public function renderError(Throwable $exception, string $renderType): string
    {
        $errorDetails = PhpError::toArray($exception);

        switch ($renderType) {
            case ErrorHandlerInterface::JSON_RENDER_TYPE: {
                return $this->renderJson($errorDetails);   
            }                             
        }
        
        return $this->page->renderApplicationError($errorDetails,null,'system')->getHtmlCode();     
    }

     /**
    * Render error
    *
    * @param array $errorDetails 
    * @return string
    */
    public function renderJson(array $errorDetails, bool $short = true): string
    {     
        $response = new ApiResponse(); 
        if ($short == false) {
            $response->field('details',PHPError::toString($errorDetails));
        }    
        $response->setError($errorDetails['message']);
        $response->setStatus('error');
        $response->setCode($errorDetails['code'] ?? 400);
       
        return $response->getResponseJson();
    }
}
