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

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Plain;
use DateTimeImmutable;
use Exception;

/**
 * JSON Web Token Authentication
*/
class Jwt
{
    /**
     * Create token
     *
     * @param mixed        $id
     * @param string       $key
     * @param integer|null $expire
     * @return string|null
     */
    public static function createToken($id, string $key, ?int $expire = null): ?string 
    {    
        try {
            $config = Configuration::forSymmetricSigner(new Sha256(),InMemory::plainText($key));
        } catch (Exception $e) {
            return null;
        }
      
        $now = new DateTimeImmutable();
        $tokenId = \base64_encode(\random_bytes(32));
        $expireTime = (empty($expire) == true) ? $now->modify('+1 week') : $expire;

        $token = $config->builder()
            ->issuedBy(DOMAIN)
            ->identifiedBy($tokenId)             
            ->issuedAt($now)
            ->expiresAt($expireTime)
            ->withClaim('user_id',$id)
            ->getToken($config->signer(),$config->signingKey());
        
        return $token->toString();
    }
    
    /**
     * Decode encrypted JWT token
     *
     * @param string $token
     * @param string $key
     * @return object|null
     */
    public static function decodeToken(string $token, string $key): ?object
    {
        try {
            $config = Configuration::forSymmetricSigner(new Sha256(),InMemory::plainText($key));
            $token = $config->parser()->parse($token);
        } catch (Exception $e) {
            return null;
        }

        return ($token instanceof Plain) ? $token : null;
    }

    /**
     * Validate token
     *
     * @param string $token
     * @return mixed
     */
    public static function validate(string $token, string $key): bool 
    {
        try {
            $config = Configuration::forSymmetricSigner(new Sha256(),InMemory::plainText($key));
            $token = $config->parser()->parse($token);
            $constraints = $config->validationConstraints();

            if (($token instanceof Plain) == false) {
                return false;
            }
            $config->validator()->assert($token, ...$constraints);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
