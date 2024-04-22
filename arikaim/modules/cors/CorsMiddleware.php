<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Cors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Arikaim\Core\Framework\Middleware\Middleware;
use Arikaim\Core\Framework\MiddlewareInterface;

/**
 * Cors middleware class
 */
class CorsMiddleware extends Middleware implements MiddlewareInterface
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
        $options = $this->getOptions();
        $config = [
            'credentials' => empty($options['credentials'] ?? null) ? 'true' : $options['credentials'],
            'origin'      => empty($options['origin'] ?? null) ? '*' : $options['origin'],
            'methods'     => empty($options['methods'] ?? null) ? 'GET, POST, PUT, DELETE, PATCH, OPTIONS' : $options['methods'],
            'headers'     => empty($options['headers'] ?? null) ? '*, Authorization' : $options['headers']
        ];

        $response = $response
                ->withHeader('Access-Control-Allow-Credentials',$config['credentials'])
                ->withHeader('Access-Control-Allow-Origin',$config['origin'])
                ->withHeader('Access-Control-Allow-Methods',$config['methods'])
                ->withHeader('Access-Control-Allow-Headers',$config['headers']);

        // is cors preflight request
        if (
            $request->hasHeader('Origin') == true &&
            $request->getMethod() == 'OPTIONS' &&
            $request->hasHeader('Access-Control-Request-Method') == true
        ) {
            \Arikaim\Core\Framework\ResponseEmiter::emitHeaders($response);
            exit();
        }

        return [$request,$response];            
    }
}
