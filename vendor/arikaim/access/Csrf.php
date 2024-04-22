<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
*/
namespace Arikaim\Core\Access;

use Arikaim\Core\Http\Session;

/**
 * Csrf token helpers
 */
class Csrf
{
    /**
     * Get saved token from session
     *
     * @param boolean $create
     * @return string|null
     */
    public static function getToken(bool $create = false)
    {
        $token = Session::get('csrf_token',null);
        return ($create == true && empty($token) == true) ? Self::createToken() : $token;        
    }

    /**
     * Return true if token is valid
     *
     * @param string $token
     * @return bool
     */
    public static function validateToken(string $token): bool
    {
        return (empty($token) == true || Self::getToken() !== $token) ? false : true; 
    }

    /**
     * Create new token and save to session
     *
     * @return string
     */
    public static function createToken(): string 
    {
        $token = \bin2hex(\random_bytes(16));
        Session::set('csrf_token',$token);

        return $token;
    }
}
