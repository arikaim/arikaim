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
use Arikaim\Core\Access\Provider\AuthProvider;

/**
 * Public auth provider.
 */
class PublicAuthProvider extends AuthProvider implements AuthProviderInterface
{
    /**
     * Authenticate
     *
     * @param array $credentials
     * @param ServerRequestInterface|null $request
     * @return boolean
     */
    public function authenticate(array $credentials, ?ServerRequestInterface $request = null): bool
    {           
        $this->user = null;

        return true;           
    }
  
    /**
     * Logout
     *
     * @return void
     */
    public function logout(): void
    {   
        $this->user = null;
    }
}
