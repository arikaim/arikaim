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
 * 
 * Swoole uri parse
 * 
 */
class Uri
{
    /**
     * Parse uri
     *
     * @param Psr\Http\Message\UriInterface $uri
     * @param object $swooleRequest
     * @return Psr\Http\Message\UriInterface
     */
    public static function parse($uri, $swooleRequest)
    {
        $server = $swooleRequest->server;
        $header = $swooleRequest->header;

        $uri = $uri->withScheme(isset($server['https']) && $server['https'] !== 'off' ? 'https' : 'http');

        if (isset($server['http_host'])) {
            $parts = \explode(':', $server['http_host']);
            $uri = $uri->withHost($parts[0]);
            if (isset($parts[1]) == true) {
                $uri = $uri->withPort((int)$parts[1]);
            }
        } elseif (isset($server['server_name'])) {
            $uri = $uri->withHost($server['server_name']);
        } elseif (isset($server['server_addr'])) {
            $uri = $uri->withHost($server['server_addr']);
        } elseif (isset($header['host']) == true) {            
            if (\str_contains($header['host'], ':')) {
                [$host,$port] = \explode(':',$header['host'], 2);
                if ($port !== '80') {
                    $uri = $uri->withPort((int)$port);
                }
            } else {
                $host = $header['host'];
            }
            $uri = $uri->withHost($host);
        }

        if (!isset($server['server_port'])) {
            return $uri;
        }

        if ($uri->getPort() === null) {
            return $uri;
        }

        $uri = $uri->withPort($server['server_port']);

        return Self::parseQuery($uri,$server);       
    } 

    /**
     *  Parse url query
     *
     * @param Psr\Http\Message\UriInterface $uri
     * @param array $server
     * @return Psr\Http\Message\UriInterface
     */
    public static function parseQuery($uri, array $server)
    {
        $hasQuery = false;
        if (isset($server['request_uri']) == true) {
            $requestUriParts = \explode('?', $server['request_uri']);
            $uri = $uri->withPath($requestUriParts[0]);
            if (isset($requestUriParts[1])) {
                $hasQuery = true;
                $uri = $uri->withQuery($requestUriParts[1]);
            }
        }

        if ($hasQuery == true) {
            return $uri;
        }

        if (!isset($server['query_string'])) {
            return $uri;
        }

        $uri = $uri->withQuery($server['query_string']);

        return $uri;
    }
}
