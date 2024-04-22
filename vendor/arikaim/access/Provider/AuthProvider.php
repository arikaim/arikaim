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

use Arikaim\Core\Access\Interfaces\UserProviderInterface;
use Arikaim\Core\Access\Interfaces\AuthProviderInterface;

/**
 * Auth provider base class.
 */
abstract class AuthProvider implements AuthProviderInterface
{
    /**
     * User provider
     *
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * Current auth user
     *
     * @var array|null
    */
    protected $user;

    /**
     * Provider params
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor
     *
     * @param UserProviderInterface $user
     * @param array $params
     */
    public function __construct(array $params = [])
    {       
        $this->user = null;
        $this->params = $params;
        $this->init();
    }

    /**
     * Get token from request header
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param bool $bearer
     * @return string|null Api token
     */
    public static function readAuthHeader(ServerRequestInterface $request, bool $bearer = true): ?string
    {   
        $headers = $request->getHeader('Authorization');
        $header = $headers[0] ?? '';
    
        if (empty($header) && \function_exists('apache_request_headers')) {
            $headers = \apache_request_headers();
            $header = $headers['Authorization'] ?? null;
        }
        $header = \trim($header ?? '');
        
        if ($bearer == true) {
            return (\preg_match('/Bearer\s+(.*)$/i',$header,$matches) == true) ? $matches[1] : null;
        }
        
        return $header;
    }

    /**
     * Check if user is logged
     *
     * @return boolean
     */
    public function isLogged(): bool
    {
        return (empty($this->getId()) == false);
    }

    /**
     * Init provider
     *
     * @return void
     */
    protected function init(): void
    {
    }

    /**
     * Get param
     *
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getParam(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * Return user provider
     *
     * @return UserProviderInterface
     */
    public function getProvider()
    {
        return $this->userProvider;
    }

    /**
     * Get current auth user
     *
     * @return array|null
     */
    public function getUser(): ?array
    {
        return $this->user;
    }

     /**
     * Get current auth id
     *
     * @return integer|null
     */
    public function getId()
    {
        return $this->user['auth_id'] ?? null;
    }

    /**
     * Set user provider
     *
     * @return void
     */
    public function setProvider(UserProviderInterface $userProvider): void
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Get login attempts 
     *
     * @return integer|null
     */
    public function getLoginAttempts(): ?int
    {
        return null;  
    }

    /**
     * Authenticate user 
     *
     * @param array $credentials
     * @param ServerRequestInterface|null $request
     * @return bool
     */
    abstract public function authenticate(array $credentials, ?ServerRequestInterface $request = null): bool;
    
    /**
     * Logout
     *
     * @return void
     */
    abstract public function logout(): void;
}
