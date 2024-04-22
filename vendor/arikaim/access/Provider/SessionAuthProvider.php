<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
 */
namespace Arikaim\Core\Access\Provider;

use Psr\Http\Message\ServerRequestInterface;

use Arikaim\Core\Access\Interfaces\AuthProviderInterface;
use Arikaim\Core\Http\Session;
use Arikaim\Core\Access\Provider\AuthProvider;
use Arikaim\Core\Models\Users;

/**
 * Session auth provider.
 */
class SessionAuthProvider extends AuthProvider implements AuthProviderInterface
{

    /**
     * Init provider
     *
     * @return void
     */
    protected function init(): void
    {
        $this->setProvider(new Users());
    }

    /**
     * Auth user
     *
     * @param array $credentials
     * @param ServerRequestInterface|null $request
     * @return bool
     */
    public function authenticate(array $credentials, ?ServerRequestInterface $request = null): bool
    {
        $user = $this->getProvider()->getUserByCredentials($credentials);
        if ($user === null) {
            $this->fail();
            return false;
        }
        // success
        $this->user = $user;
        $this->success();

        return true;
    }
    
    /**
     * Fail auth
     *
     * @return void
     */
    protected function fail(): void
    {
        // fail to auth
        $loginAttempts = $this->getLoginAttempts() + 1;
        Session::set('auth.login.attempts',$loginAttempts);
    } 

    /**
     * Scucess auth
     *
     * @return void
     */
    protected function success(): void
    {
        Session::set('auth.id',$this->user['auth_id'] ?? null);
        Session::set('auth.login.time',time());
        Session::remove('auth.login.attempts'); 
    }

    /**
     * Logout
     *
     * @return void
     */
    public function logout(): void
    {
        $this->user = null;
        Session::remove('auth.id');
        Session::remove('auth.login.time');
        Session::remove('auth.login.attempts');  
    }

    /**
     * Get current auth user
     *
     * @return array|null
    */
    public function getUser(): ?array
    {
        $authId = $this->getId();
        
        return (empty($authId) == true) ? null : $this->getProvider()->getUserById($authId);
    }

    /**
     * Gte auth id
     *
     * @return null|integer
     */
    public function getId()
    {
        return (int)Session::get('auth.id',null);     
    }

    /**
     * Get login attempts 
     *
     * @return integer
     */
    public function getLoginAttempts(): ?int
    {
        return (int)Session::get('auth.login.attempts',0);  
    }
}
