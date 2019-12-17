<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Http;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Slim\ResponseEmitter;
use Psr\Http\Message\ResponseInterface;

/**
 * Response helpers
 */
class Response 
{ 
    /**
     * Create response
     *
     * @param integer $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public static function create($code = 200, $reasonPhrase = '')
    {
        return new GuzzleResponse($code, [], null, '1.1', $reasonPhrase);
    }

    /**
     * Emit reponse
     *
     * @param ResponseInterface $response
     * @return string
     */
    public static function emit(ResponseInterface $response)
    {
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }
}