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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Arikaim\Core\Framework\MiddlewareInterface;
use Arikaim\Core\Access\Middleware\AuthMiddleware;
use Arikaim\Core\Access\Csrf;

/**
 * Verify Csrf token middleware
 */
class CsrfToken extends AuthMiddleware implements MiddlewareInterface
{
    /**
     * Process middleware
     * 
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
    */
    public function process(ServerRequestInterface $request, ResponseInterface $response): array
    {
        if (\in_array($request->getMethod(),['POST','PUT','DELETE','PATCH']) == true) {
            $token = $this->getToken($request);

            if (Csrf::validateToken($token) == false) {   
                $request = $this->generateToken($request);  
                // token error
                return [$request,$this->handleError($response)];                                                 
            }
        }
        $request = $this->generateToken($request);     

        return [$request,$response];
    }

    /**
     * Vreate new token if middleware param recreate_token is set to true
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function generateToken(ServerRequestInterface $request)
    {
        if ($this->getOption('recreate_token') == true) {
            $token = Csrf::createToken();
            $request = $request->withAttribute('csrf_token', $token);
        }    

        return $request;
    }

    /**
     * Get csrf token from request
     *
     * @param ServerRequestInterface $request
     * @return string|null
     */
    public function getToken(ServerRequestInterface $request)
    {
        $body = $request->getParsedBody();
        $body = (empty($body) == true) ? [] : $body;
        $token = $body['csrf_token'] ?? null;

        if (empty($token) == true) {          
            $token = $request->getHeaderLine('X-XSRF-TOKEN');
        }

        return $token;
    }    
}
