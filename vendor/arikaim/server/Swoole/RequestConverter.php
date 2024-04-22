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

use Arikaim\Core\Server\Swoole\Uri;
use Arikaim\Core\Server\Swoole\UploadedFiles;

class RequestConverter
{
    /**
     * Convert swoole requets to psr7 request object
     *
     * @param Swoole\HTTP\Request $swooleRequest
     * @param object $factory
     * @return object
     */
    public static function convert($swooleRequest, $factory) 
    { 
        $server = $swooleRequest->server;
        $method = $server['request_method'] ?? 'GET';
        $files = $swooleRequest->files ?? [];

        $uri = Uri::parse($factory->createUri(),$swooleRequest);
        $request = $factory->createServerRequest($method,$uri);
        $serverRequest = Self::addHeaders($swooleRequest,$request);
        $stream = $factory->createStreamFromResource($swooleRequest->rawContent());

        return $serverRequest
            ->withProtocolVersion(Self::parseProtocol($server))
            ->withCookieParams($swooleRequest->cookie ?? [])
            ->withQueryParams($swooleRequest->get ?? [])
            ->withParsedBody($swooleRequest->post ?? [])
            ->withBody($stream)
            ->withUploadedFiles(UploadedFiles::parse($files,$factory));
    }

    /**
     * Add headers
     *
     * @param Swoole\HTTP\Request $swooleRequest
     * @param object $request
     * @return object
     */
    private static function addHeaders($swooleRequest, $request) 
    {
        $headers = $swooleRequest->header ?? [];
        foreach ($headers as $name => $value) {
            if ($request->hasHeader($name) == true) {
                continue;
            }

            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }

    /**
     * Parse protocol
     *
     * @param array $server
     * @return string
     */
    private static function parseProtocol(array $server): string
    { 
        $protocol = (isset($server['server_protocol']) == true) ? \str_replace('HTTP/','',$server['server_protocol']) : '1.1';

        return (\is_string($protocol) == true) ? $protocol : '1.1';
    }
}
