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

/**
 * Error renderer interface
 */
interface ErrorRendererInterface
{  
    /**
     * Render error
     *
     * @param array $errorDetails
     * @return void
     */
    public function render($errorDetails);
}
