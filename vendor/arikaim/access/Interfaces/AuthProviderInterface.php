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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Auth provider interface
 */
interface AuthProviderInterface
{    
    /**
     * Logout
     *
     * @return void
     */
    public function logout(): void;

    /**
     * Get current auth user
     *
     * @return array|null
     */
    public function getUser(): ?array;
    
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
     * @param ServerRequestInterface|null $request
     * @return bool
     */
    public function authenticate(array $credentials, ?ServerRequestInterface $request = null): bool;

    /**
     * Get login attempts
     *
     * @return integer|null
     */
    public function getLoginAttempts(): ?int;

    /**
     * Check if user is logged
     *
     * @return boolean
     */
    public function isLogged(): bool;
}
