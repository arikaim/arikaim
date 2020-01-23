<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

use Arikaim\Core\Http\Url;

/**
 * AccessToken trait
*/
trait AccessToken 
{        
    /**
     * Create protected Url
     *
     * @param integer $userId
     * @param string $pattern
     * @return string|false
     */
    public function createProtectedUrl($userId, $pattern)
    {
        $accessToken = $this->get('access')->provider('token')->createToken($userId);   

        return (is_object($accessToken) == true) ? Url::BASE_URL . '/' . $pattern . '/' . $accessToken->token : false;
    }
}
