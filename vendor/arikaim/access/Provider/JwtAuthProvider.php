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
use Arikaim\Core\Access\Jwt;
use Arikaim\Core\Models\Users;

/**
 * JWT auth provider.
 */
class JwtAuthProvider extends AuthProvider implements AuthProviderInterface
{
    /**
     * JWT token
     *
     * @var object|null
     */
    private $token;

    /**
     * Jwt key
     *
     * @var string
     */
    private $jwtKey;

    /**
     * Init provider
     *
     * @return void
     */
    protected function init(): void
    {
        $this->jwtKey = $this->getParam('key');
        $this->clearToken();
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
        $token = $credentials['token'] ?? null;
        $token = (empty($token) == true) ? AuthProvider::readAuthHeader($request,false) : $token;
        if (empty($token) == true) {         
            return false;
        }

        if ($this->decodeToken($token) == false) {
            return false;
        }

        $id = $this->getTokenParam('user_id');
        if (empty($id) == true) {
            return false;
        }

        $this->user = $this->getProvider()->getUserById($id);
        
        if ($this->user === null) {
            $this->clearToken();
            return false;
        }

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
        $this->clearToken();
    }

    /**
     * Get auth id
     *
     * @return null|integer
     */
    public function getId()
    {
        return $this->getTokenParam('user_id');       
    }

    /**
     * Remove token.
     *
     * @return void
     */
    public function clearToken(): void
    {
        $this->token = null;
    }

    /**
     * Return true if token is valid
     *
     * @return boolean
     */
    public function isValidToken(): bool
    {
        return ($this->token != null);           
    }

    /**
     * Create auth token.
     *
     * @param mixed $id Auth id
     * @param integer|null $expire
     * @param string|null $key
     * @return string
     */
    public function createToken($id, ?int $expire = null, ?string $key = null) 
    {
        return Jwt::createToken($id,$key ?? $this->jwtKey,$expire);              
    }

    /**
     * Decode and save token data.
     *
     * @param string $token
     * @param int|null $expire
     * @param string|null $key
     * @return boolean
     */
    public function decodeToken(string $token, ?string $key = null): bool
    {       
        $key = (empty($key) == true) ? $this->jwtKey : $key;

        $this->token = Jwt::decodeToken($token,$key);

        return ($this->token != null);         
    }

    /**
     * Return token array data
     *
     * @return object|null
     */
    public function getToken(): ?object
    {
        return $this->token;
    }

    /**
     * Return token param from decoded token
     *
     * @param string $name
     * @return mixed|null
     */
    public function getTokenParam(string $name)
    {  
        return ($this->token == null) ? null : $this->token->claims()->get($name);
    }
}
