<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Server\Swoole;

/**
 * Convert psr response to swoole response
 */
class ResponseConverter
{
    /**
     * Resposne convert
     *
     * @param object $response
     * @param Swoole\HTTP\Response $swooleResponse
     * @return Swoole\HTTP\Response
     */
    public static function convert($response, $swooleResponse) 
    { 
        $headers = $response->getHeaders();
        if (empty($headers) == false) {
            $response = $response->withoutHeader('Set-Cookie');
            foreach ($headers as $key => $header) {
                $swooleResponse->header($key,\implode('; ', $header));
            }
        }

        $swooleResponse->status($response->getStatusCode(),$response->getReasonPhrase());

        if ($response->getBody()->getSize() > 0) {
            if ($response->getBody()->isSeekable()) {
                $response->getBody()->rewind();
            }
            $swooleResponse->write($response->getBody()->getContents());
        }

        return $swooleResponse;
    }
}
