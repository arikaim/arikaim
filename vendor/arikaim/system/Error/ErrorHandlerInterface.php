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

use Throwable;

/**
 * Error handler interface
 */
interface ErrorHandlerInterface
{  
    const JSON_RENDER_TYPE    = 'json';
    const HTML_RENDER_TYPE    = 'html';
    const CONSOLE_RENDER_TYPE = 'console';

    /**
     * Render error
     *
     * @param Throwable  $exception The caught Throwable object
     * @param string $renderType   
     * @return string   
     */
    public function renderError(Throwable $exception, string $renderType): string;
}
