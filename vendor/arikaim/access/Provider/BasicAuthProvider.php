<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Access\Provider;

use Arikaim\Core\Access\Interfaces\AuthProviderInterface;
use Arikaim\Core\Access\Provider\AuthProvider;

/**
 * Basic auth provider.
 */
class BasicAuthProvider extends AuthProvider implements AuthProviderInterface
{
    /**
     * Auth user
     *
     * @param array $credentials
     * @return bool
     */
    public function authenticate(array $credentials)
    {
        $password = (isset($credentials['password']) == true) ? $credentials['password'] : null;

        $this->user = $this->getProvider()->getUserByCredentials($credentials);
        if ($this->user === false) {
            return false;
        }
      
        return ($this->user->verifyPassword($password) == true);                
    }
  
    /**
     * Logout
     *
     * @return void
     */
    public function logout()
    {
        $this->user = null;
    }
}
