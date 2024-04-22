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
use Arikaim\Core\Db\Model;
use Arikaim\Core\Access\Interfaces\AuthTokensInterface;

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
    public function createProtectedUrl(int $userId, string $pattern)
    {
        // page acess token
        $accessToken = Model::AccessTokens()->createToken($userId,AuthTokensInterface::PAGE_ACCESS_TOKEN,1800);

        return (\is_array($accessToken) == true) ? Url::BASE_URL . '/' . $pattern . '/' . $accessToken['token'] : false;
    }
}
