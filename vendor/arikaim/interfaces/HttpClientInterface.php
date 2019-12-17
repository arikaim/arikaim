<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Interfaces;

/**
 * Http Client Interface
 */
interface HttpClientInterface
{    
    /**
     * Fetch url
     *
     * @param string $url
     * @param array $options
     * @return string|null
     */
    public function fetch($url, $options = []);

    /**
     * Create and send an GET request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. 
     *
     * @return ResponseInterface
     */
    public function get($uri, array $options = []);

    /**
     * Create and send an HEAD request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function head($uri, array $options = []);
    
    /**
     * Create and send an PUT request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function put($uri, array $options = []);

    /**
     * Create and send an DELETE request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function delete($uri, array $options = []);

    /**
     * Create and send an POST request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function post($uri, array $options = []);

    /**
     * Create and send an OPTIONS request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function options($uri, array $options = []);

    /**
     * Create and send an PATCH request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function patch($uri, array $options = []);
    
    /**
     * Create and send an TRACE request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function trace($uri, array $options = []);
    
    /**
     * Create and send an CONNECT request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function connect($uri, array $options = []);
}
