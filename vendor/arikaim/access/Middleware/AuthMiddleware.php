<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Access\Middleware;

use Arikaim\Core\Interfaces\SystemErrorInterface;
use Arikaim\Core\Access\Interfaces\AuthProviderInterface;

/**
 *  Middleware base class
 */
class AuthMiddleware
{
    /**
     * Auth provider
     *
     * @var Arikaim\Core\Access\Interfaces\AuthProviderInterface
     */
    protected $auth;

    /**
     * System error renderer
     *
     * @var SystemErrorInterface
     */
    private $errorRenderer;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(AuthProviderInterface $auth, SystemErrorInterface $errorRenderer)
    {
       $this->auth = $auth;
    }
    
    /**
     * Get auth provider
     *
     * @return Arikaim\Core\Access\Interfaces\AuthProviderInterface
     */
    public function getAuthProvider()
    {
        return $this->auth;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return string
     */
    protected function resolveAuthError($request)
    {
        return $this->errorRenderer->renderSystemErrors($request,"AUTH_FAILED");       
    }
}
