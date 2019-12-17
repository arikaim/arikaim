<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Access\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(),['POST','PUT','DELETE','PATCH']) == true) {
            $token = $this->getToken($request);

            if (Csrf::validateToken($token) == false) {   
                $request = $this->generateToken($request);  
                // token error
                return $this->resolveAuthError($request);                                                  
            }
        }
        $request = $this->generateToken($request);     

        return $handler->handle($request);
    }

    /**
     * Vreate new token if middleware param recreate_token is set to true
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function generateToken(ServerRequestInterface $request)
    {
        if ($this->getParam('recreate_token') == true) {
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
    public function getToken(ResponseInterface $request)
    {
        $body = $request->getParsedBody();
        $body = (empty($body) == true) ? [] : $body;
        $token = isset($body['csrf_token']) ? $body['csrf_token'] : null;

        if (empty($token) == true) {          
            $token = $request->getHeaderLine('X-XSRF-TOKEN');
        }

        return $token;
    }    
}
