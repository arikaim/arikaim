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
use Arikaim\Core\Access\Provider\AuthProvider;
use Arikaim\Core\Access\Jwt;

/**
 * JWT auth provider.
 */
class JwtAuthProvider extends AuthProvider implements AuthProviderInterface
{
    /**
     * JWT token
     *
     * @var array
     */
    private $token;

    /**
     * Jwt key
     *
     * @var string
     */
    private $jwtKey;

    /**
     * Constructor
     *
     * @param UserProviderInterface $user
     * @param array $params
     */
    public function __construct(UserProviderInterface $userProvider, $params = [])
    {    
        parent::__construct($userProvider,$params);   
      
        $this->jwtKey = $this->getParam('key');
        $this->clearToken();
    }

    /**
     * Auth user
     *
     * @param array $credentials
     * @return bool
     */
    public function authenticate(array $credentials)
    {
        $token = (isset($credentials['token']) == false) ? null : $credentials['token'];
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

        $this->user = $this->getProvider()->getUserByCredentials(['id' => $id]);
        if ($this->user === false) {
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
    public function logout()
    {
        $this->user = null;
        $this->clearToken();
    }

    /**
     * Gte auth id
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
    public function clearToken()
    {
        $this->token['decoded'] = null;
        $this->token['token'] = null;
    }

    /**
     * Return true if token is valid
     *
     * @return boolean
     */
    public function isValidToken()
    {
        return !empty($this->token['decoded']);           
    }

    /**
     * Create auth token.
     *
     * @param mixed $id Auth id
     * @param integer|null $expire
     * @param string|null $key
     * @return object
     */
    public function createToken($id, $expire = null, $key = null) 
    {
        $key = (empty($key) == true) ? $this->jwtKey: $key;
        $jwt = new Jwt($expire,$key);
        $jwt->set('user_id',$id);   

        return $jwt->createToken();       
    }

    /**
     * Decode and save token data.
     *
     * @param string $tokens
     * @return boolean
     */
    public function decodeToken($token, $expire = null, $key = null)
    {       
        $key = (empty($key) == true) ? $this->jwtKey: $key;
        $jwt = new Jwt($expire,$key);

        $decoded = $jwt->decodeToken($token);
        $decoded = ($decoded === false) ? null : $decoded;

        $this->token['token'] = $token;
        $this->token['decoded'] = $decoded;
       
        return !empty($decoded);
    }

    /**
     * Return token array data
     *
     * @return array
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Return token param from decoded token
     *
     * @param string $name
     * @return mixed|null
     */
    public function getTokenParam($name)
    {
        if (isset($this->token['decoded'][$name]) == false) {
            return null;
        }
        if (is_object($this->token['decoded'][$name]) == true) {            
            return $this->token['decoded'][$name]->getValue();
        }

        return null;
    }
}
