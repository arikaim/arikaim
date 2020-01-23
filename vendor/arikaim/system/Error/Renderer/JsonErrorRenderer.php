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

use Arikaim\Core\Http\ApiResponse;
use Arikaim\Core\System\Error\ErrorRendererInterface;

/**
 * Render error
 */
class JsonErrorRenderer implements ErrorRendererInterface
{
    /**
    * Render error
    *
    * @param array $errorDetails 
    * @return string
    */
    public function render($errorDetails)
    {
        $response = new ApiResponse();
        $response->setError($errorDetails['message']);
        
        return $response->getResponseJson();
    }
}
