<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * @package     Access
*/
namespace Arikaim\Core\Access\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Arikaim\Core\Framework\Middleware\Middleware;
use Arikaim\Core\Framework\MiddlewareInterface;
use Arikaim\Core\Access\Interfaces\AuthProviderInterface;

/**
 *  Auth Middleware base class
 */
class AuthMiddleware extends Middleware implements MiddlewareInterface
{
    /**
     * Auth provider
     *
     * @var array
     */
    protected $authProviders;

    /**
     * Constructor
     *
     * @param ContainerInterface|null
     * @param array|null $options
     */
    public function __construct($container = null, ?array $options = [])
    {
        parent::__construct($container,$options);
        $this->authProviders = $options['authProviders'] ?? [];  
    }

    /**
     * Set Auth providers
     *
     * @param array $authProviders
     * @return void
     */
    public function setAuthProviders(array $authProviders): void
    {
        $this->authProviders = $authProviders; 
    }

    /**
     * Process middleware
     * 
     * @param ServerRequestInterface  $request  
     * @return ResponseInterface
    */
    public function process(ServerRequestInterface $request, ResponseInterface $response): array
    {             
        foreach ($this->authProviders as $provider) {

            if ($provider->isLogged() == true) {
                $this->container->get('access')->withProvider($provider);     
                return [$request,$response];
            }
            
            if ($provider->authenticate([],$request) == true) {
                // success
                $this->container->get('access')->withProvider($provider);     
                return [$request,$response];
            } 
        }
             
        return [$request,$this->handleError($response)];
    }

    /**
     * Get auth provider
     *
     * @return AuthProviderInterface|null
     */
    public function getAuthProvider($name): ?AuthProviderInterface
    {
        return $this->authProviders[$name] ?? null;
    }

    /**
     * Show auth error
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function handleError($response): ResponseInterface
    {             
        if (empty($this->options['redirect'] ?? null) == false) { 
            // Set response redirect     
            $response = $response
                ->withoutHeader('Cache-Control')
                ->withHeader('Cache-Control','no-cache, must-revalidate')   
                ->withHeader('Expires','Sat, 26 Jul 1997 05:00:00 GMT')        
                ->withHeader('Location',$this->options['redirect']);                          
        } 

        return $response->withStatus(401);       
    }    
}
