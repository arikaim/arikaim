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
    const UNKNOWN_ERROR_CODE = 'UNKNOWN_ERROR';

    /**
     * Get error
     *
     * @param string $errorCode
     * @param string|null $default
     * @param array $params
     * @return string|null
     */
    public function getError(string $errorCode, array $params = [], ?string $default = null): ?string;
}
