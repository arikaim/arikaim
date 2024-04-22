<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Http\Interfaces;

/**
 * Http Client Interface
 */
interface HttpClientAdapterInterface
{    
    /**
     * Create and send an HTTP request.
     *
     * @param string              $method  HTTP method.
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. 
     *
     * @return ResponseInterface
     */
    public function request($method, $uri, array $options = []);
}
