<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces\Access;

use Arikaim\Core\Interfaces\Access\AccessInterface;

/**
 * Auth interface
 */
interface AuthInterface extends AccessInterface
{    
    /**
     * Logout
     *
     * @return void
     */
    public function logout();

    /**
     * Get current auth user
     *
     * @return array|null
     */
    public function getUser();
    
    /**
     * Get current auth id
     *
     * @return integer|null
     */
    public function getId();

    /**
     * Authenticate user 
     *
     * @param array $credentials
     * @return bool
     */
    public function authenticate(array $credentials);

    /**
     * Get login attempts
     *
     * @return integer|null
     */
    public function getLoginAttempts();

    /**
     * Get auth name
     *
     * @param string|integer $type
     * @return null|integer
     */
    public function resolveAuthType($type);

    /**
     * Return true if user is logged
     *
     * @return boolean
     */
    public function isLogged();

    /**
     * Create auth middleware
     *
     * @param string $authName
     * @param array $args
     * @return object|null
     */
    public function middleware($authName, $args = null);

    /**
     * Return auth provider
     *
     * @return AuthProviderInterface
     */
    public function getProvider();

    /**
     * Change auth provider
     *
     * @param AuthProviderInterface|string $provider
     * @param UserProviderInterface|null $user
     * @param array $params
     * @return AuthProviderInterface
     */
    public function withProvider($provider, $user = null, $params = []);
}
