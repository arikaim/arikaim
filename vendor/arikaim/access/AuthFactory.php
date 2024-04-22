<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
 */
namespace Arikaim\Core\Access;

use Arikaim\Core\Access\Middleware\AuthMiddleware;

/**
 * Auth factory class.
 */
class AuthFactory
{
    // auth type id
    const AUTH_BASIC        = 'basic';
    const AUTH_SESSION      = 'session';
    const AUTH_JWT          = 'jwt';
    const AUTH_TOKEN        = 'token';
    const CSRF_TOKEN        = 'csrf';
    const OAUTH_TOKEN       = 'oauth';
    const AUTH_PUBLIC       = 'public';

    /**
     * Providers object pool
     *
     * @var array
     */
    private static $providers = [];

    /**
     * Auth name
     *
     * @var array
     */
    private static $authNames = [
        Self::AUTH_BASIC,
        Self::AUTH_SESSION,
        Self::AUTH_JWT,
        Self::AUTH_TOKEN,
        Self::CSRF_TOKEN,
        Self::OAUTH_TOKEN,
        Self::AUTH_PUBLIC     
    ];

    /**
     * Auth provider classes
     *
     * @var array
     */
    private static $providerClasses = [      
        Self::AUTH_BASIC   => 'Arikaim\\Core\\Access\\Provider\\BasicAuthProvider',
        Self::AUTH_SESSION => 'Arikaim\\Core\\Access\\Provider\\SessionAuthProvider',
        Self::AUTH_JWT     => 'Arikaim\\Core\\Access\\Provider\\JwtAuthProvider',
        Self::AUTH_TOKEN   => 'Arikaim\\Core\\Access\\Provider\\TokenAuthProvider',
        Self::OAUTH_TOKEN  => 'Arikaim\\Core\\Access\\Provider\\OauthProvider',
        Self::AUTH_PUBLIC  => 'Arikaim\\Core\\Access\\Provider\\PublicAuthProvider'         
    ];

    /**
     * Create auth provider
     *
     * @param string $name
     * @param array $params
     * @return object|null
     */
    public static function createProvider(string $name, array $params = []): ?object
    {
        if (isset(Self::$providers[$name]) == true) {
            return Self::$providers[$name];
        }

        $class = Self::$providerClasses[$name] ?? $name; 
        if (empty($class) == true) {
            return null;
        }     

        Self::$providers[$name] = new $class($params);
        
        return Self::$providers[$name];
    }

    /**
     * Create auth middleware
     *
     * @param string $authName   
     * @param object|null $container
     * @param array $options
     * @return object|null
     */
    public static function createMiddleware(string $authName, ?object $container = null, array $options = [])
    {            
        $options['authProviders'] = Self::createAuthProviders($authName,$options);

        return (\count($options['authProviders']) == 0) ? null : new AuthMiddleware($container,$options);              
    }
    
    /**
     * Create auth providers
     *
     * @param string|array $authName
     * @param array $params
     * @return array
     */
    public static function createAuthProviders($authName, array $params = []): array
    {
        $providers = (\is_array($authName) == false) ? \explode(',',$authName) : $authName;

        $result = [];
        foreach ($providers as $item) {
            $result[$item] = Self::createProvider($item,$params);
        }

        return $result;
    } 

    /**
     * Check if auth name is valid 
     *
     * @param string $name
     * @return boolean
     */
    public static function isValidAuthName(string $name): bool
    {
        return (\array_search($name,Self::$authNames) !== false);
    }

    /**
     * Resolve auth type
     *
     * @param string|null|array $type
     * @return null|string
     */
    public static function resolveAuthType($type): ?string
    {
        if (\is_array($type) == true) {
            return \implode(',',$type);
        }
        if (empty($type) == true) {
            return null;
        }
      
        return \trim((string)$type ?? '');
    }

    /**
     * Get auth provider class
     *
     * @param string $name
     * @return string
     */
    public static function getAuthProviderClass(string $name): string
    {
        return Self::$providerClasses[$name] ?? '';
    }
}
