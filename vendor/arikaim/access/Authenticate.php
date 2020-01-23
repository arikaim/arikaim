<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Access;

use Arikaim\Core\Access\Provider\SessionAuthProvider;
use Arikaim\Core\Access\Interfaces\UserProviderInterface;
use Arikaim\Core\Access\Interfaces\AuthProviderInterface;
use Arikaim\Core\Interfaces\SystemErrorInterface;
use Arikaim\Core\Interfaces\Access\AuthInterface;
use Arikaim\Core\Interfaces\Access\AccessInterface;

/**
 * Manage auth.
 */
class Authenticate implements AuthInterface, AccessInterface
{
    const ACCESS_NAMESPACE = "Arikaim\\Core\\Access\\";
   
    // auth type id
    const AUTH_BASIC        = 1;
    const AUTH_SESSION      = 2;
    const AUTH_JWT          = 3;
    const AUTH_TOKEN        = 4;
    const CSRF_TOKEN        = 5;

    /**
     * Auth name
     *
     * @var array
     */
    private $authNames = ["none","basic","session","jwt",'token','csrf'];

    /**
     * Auth provider variable
     *
     * @var AuthProviderInterface
     */
    private $provider;

    /**
     * Auth user
     *
     * @var UserProviderInterface
     */
    private $user;

    /**
     * Permissins manager
     *
     * @var AccessInterface
     */
    private $access;

    /**
     * System error renderer
     *
     * @var SystemErrorInterface
     */
    private $errorRenderer;

    /**
     * Constructor
     *
     * @param UserProviderInterface $user
     * @param AccessInterface $access
     * @param AuthProviderInterface $provider
     */
    public function __construct(
        UserProviderInterface $user, 
        AccessInterface $access, 
        SystemErrorInterface $errorRenderer,
        AuthProviderInterface $provider = null)
    {       
        $this->user = $user;
        $this->provider = ($provider == null) ? new SessionAuthProvider($user) : $provider;   
        $this->access = $access;
        $this->errorRenderer = $errorRenderer;
    }

    /**
     * Add permission item.
     *
     * @param string $name    
     * @param string|null $title
     * @param string|null $description
     * @param string|null $extension
     * @return boolean
     */
    public function addPermission($name, $title = null, $description = null, $extension = null)
    {
        return $this->access->addPermission($name,$title,$description,$extension);
    }

    /**
     * Full Permissions 
     *
     * @return array
     */
    public function getFullPermissions()
    {
        return $this->access->getFullPermissions();
    }

    /**
     * Control panel permission name
     *
     * @return string
     */
    public function getControlPanelPermission()
    {
        return $this->access->getControlPanelPermission();
    }

    /**
     * Check if current loged user have control panel access
     *
     * @return boolean
     */
    public function hasControlPanelAccess($authId = null)
    {
        $authId = (empty($authId) == true) ? $this->getId() : $authId;

        return (empty($authId) == true) ? false : $this->access->hasControlPanelAccess($authId);
    }

    /**
     * Check access 
     *
     * @param string $name Permission name
     * @param string|array $type PermissionType (read,write,execute,delete)    
     * @return boolean
    */
    public function hasAccess($name, $type = null, $authId = null)
    {
        $authId = (empty($authId) == true) ? $this->getId() : $authId;
      
        return (empty($authId) == true) ? false : $this->access->hasAccess($name,$type,$authId);
    }
    
    /**
     * Resolve permission full name  name:type
     *
     * @param string $name
     * @return array
     */
    public function resolvePermissionName($name)
    {
        return $this->access->resolvePermissionName($name);
    }

    /**
     * Return auth provider
     *
     * @return AuthProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set auth provider
     *
     * @param AuthProviderInterface $provider
     * @return void
     */
    public function setProvider(AuthProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Change auth provider
     *
     * @param AuthProviderInterface|string $provider
     * @param UserProviderInterface|null $user
     * @param array $params
     * @return Authenticate
     */
    public function withProvider($provider, $user = null, $params = [])
    {
        if (is_string($provider) == true) {
            $provider = $this->createProvider($provider,$user,$params);
        }
        $this->setProvider($provider);

        return $this;
    }

    /**
     * Create auth provider
     *
     * @param string $name
     * @param UserProviderInterface|null $user
     * @param array $params
     * @return object|null
     */
    protected function createProvider($name, UserProviderInterface $user = null, $params = [])
    {
        $className = (class_exists($name) == true) ? $name : $this->getAuthProviderClass($this->resolveAuthType($name));
        $fullClassName = Self::ACCESS_NAMESPACE . "Provider\\" . $className;
        $user = (empty($user) == true) ? $this->user : $user;

        return (class_exists($fullClassName) == true) ? new $fullClassName($user,$params) : null;
    }

    /**
     * Create auth middleware
     *
     * @param string $authName
     * @param array $options
     * @return object|null
     */
    public function middleware($authName, $options = [])
    {       
        $className = (class_exists($authName) == true) ? $authName : $this->getAuthMiddlewareClass($this->resolveAuthType($authName));
        $fullClassName = Self::ACCESS_NAMESPACE . "Middleware\\" . $className;
        
        return (class_exists($fullClassName) == true) ? new $fullClassName($this->provider,$this->errorRenderer,$options) : null;
    }

    /**
     * Auth user 
     *
     * @param array $credentials
     * @return bool
     */
    public function authenticate(array $credentials)
    {
        return $this->provider->authenticate($credentials);
    }
    
    /**
     * Logout
     *
     * @return void
     */
    public function logout()
    {
        $this->provider->logout();
    }

    /**
     * Get logged user
     *
     * @return mixed|null
     */
    public function getUser()
    {
        return $this->provider->getUser();
    }

    /**
     * Get login attempts
     *
     * @return null|integer
     */
    public function getLoginAttempts()
    {
        return $this->provider->getLoginAttempts();
    }

    /**
     * Get auth id
     *
     * @return null|integer
     */
    public function getId()
    {
        return $this->provider->getId();
    }

    /**
     * Return true if user is logged
     *
     * @return boolean
     */
    public function isLogged()
    {
        return !empty($this->getId());
    }

    /**
     * Return auth name
     *
     * @param int $auth
     * @return string
     */
    public function getAuthName($auth)
    {
        return (isset($this->authNames[$auth]) == true) ? $this->authNames[$auth] : false;          
    }

    /**
     * Return auth type id
     *
     * @param string $name
     * @return int
     */
    public function getTypeId($name)
    {
        return array_search($name,$this->authNames);                 
    }

    /**
     * Check if auth name is valid 
     *
     * @param string $name
     * @return boolean
     */
    public function isValidAuthName($name)
    {
        return (array_search($name,$this->authNames) === false) ? false : true;     
    }

    /**
     * Resolve auth type
     *
     * @param string|integer $type
     * @return null|integer
     */
    public function resolveAuthType($type)
    {
        if (is_string($type) == true) {
            return $this->getTypeId($type);
        }

        return (is_integer($type) == true) ? $type : null;
    }

    /**
     * Get middleware class name
     *
     * @param integer $id
     * @return string|null
     */
    public function getAuthMiddlewareClass($id)
    {
        $classes = [
            null,
            'BasicAuthentication',
            'SessionAuthentication',
            'JwtAuthentication',
            'TokenAuthentication',
            'CsrfToken'
        ];

        return (isset($classes[$id]) == true) ? $classes[$id] : null;
    }

    /**
     * Get auth provider class
     *
     * @param ineteger $id
     * @return string|null
     */
    public function getAuthProviderClass($id)
    {
        $classes = [
            null,
            'BasicAuthProvider',
            'SessionAuthProvider',
            'JwtAuthProvider',
            'TokenAuthProvider'
        ];

        return (isset($classes[$id]) == true) ? $classes[$id] : null;
    }
}
