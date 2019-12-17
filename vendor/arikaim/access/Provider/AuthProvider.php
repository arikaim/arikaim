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
     * @return UserProviderInterface
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
    public function __construct(UserProviderInterface $userProvider, $params = [])
    {       
        $this->userProvider = $userProvider;
        $this->user = null;
        $this->params = $params;
    }

    /**
     * Get param
     *
     * @param string $name
     * @return mixed|null
     */
    public function getParam($name)
    {
        return (isset($this->parms[$name]) == true) ? $this->parms[$name] : null;
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
     * @return UserProviderInterface
     */
    public function getUser()
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
        return (empty($this->user) == false) ? $this->user->getAuthId() : null;
    }

    /**
     * Set user provider
     *
     * @return void
     */
    public function setProvider(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Get login attempts 
     *
     * @return integer|null
     */
    public function getLoginAttempts()
    {
        return null;  
    }

    /**
     * Authenticate user 
     *
     * @param array $credentials
     * @return bool
     */
    abstract public function authenticate(array $credentials);
    
    /**
     * Logout
     *
     * @return void
     */
    abstract public function logout();
}
