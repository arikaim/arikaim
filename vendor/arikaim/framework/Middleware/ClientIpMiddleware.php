<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Arikaim\Core\Utils\ClientIp;
use Arikaim\Core\Framework\Middleware\Middleware;
use Arikaim\Core\Framework\MiddlewareInterface;

/**
 * Cient Ip middleware
 */
class ClientIpMiddleware extends Middleware implements MiddlewareInterface
{ 
    /**
     * Process middleware 
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return array [$request,$response]
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): array
    {    
        $request = $request->withAttribute('client_ip',ClientIp::getClientIpAddress($request));   
        // referer
        $request = $request->withAttribute('referer',$_SERVER['HTTP_REFERER'] ?? null);   
       
        return [$request,$response];
    }   
}
