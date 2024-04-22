<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
*/
namespace Arikaim\Core\Access\Interfaces;

/**
 * Auth tokens interface
 */
interface AuthTokensInterface
{    
    /**
     * Token access type
     */
    const PAGE_ACCESS_TOKEN  = 0;
    const LOGIN_ACCESS_TOKEN = 1;
    const API_ACCESS_TOKEN   = 2;
    const OAUTH_ACCESS_TOKEN = 3;
}
