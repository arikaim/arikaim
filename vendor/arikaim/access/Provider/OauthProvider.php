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
use Arikaim\Core\Access\Provider\SessionAuthProvider;
use Arikaim\Core\Models\Users;

/**
 * OAuth provider.
 */
class OauthProvider extends SessionAuthProvider implements AuthProviderInterface
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
        
        $this->user = $user;
        $this->success();
               
        return true;
    }
}
