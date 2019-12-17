<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Stats;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Arikaim\Core\Extension\Module;
use Arikaim\Core\Arikaim;

/**
 * Stats middleware module class
 */
class Stats extends Module implements MiddlewareInterface
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
        $uri = $request->getUri();
        $uriInfo = [
            'scheme'   => $uri->getScheme(),
            'user'     => $uri->getUserInfo(),
            'host'     => $uri->getHost(),
            'path'     => $uri->getPath(),
            'fragment' => $uri->getFragment()
        ];

        // dispatch event
        Arikaim::event()->dispatch('stats.middleware',[
            'method'          => $request->getMethod(),
            'query_params'    => $request->getQueryParams(),
            'uri'             => $uriInfo,               
            'url'             => (string)$request->getUri(),
            'client_ip'       => $request->getAttribute('client_ip'),
            'http_user_agent' => $request->getheader('HTTP_USER_AGENT')
        ]);

        return $handler->handle($request);
    }
}
