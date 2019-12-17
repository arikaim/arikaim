<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Extension interface
 */
interface SystemErrorInterface 
{  
    /**
     * Return error message
     *
     * @param Request $request
     * @param string|null $error
     * @return string
     */
    public function renderSystemErrors($request, $error = null);
}
