<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Http;

use Arikaim\Core\Interfaces\HttpClientInterface;
use Arikaim\Core\Http\Interfaces\HttpClientAdapterInterface;
use Arikaim\Core\Http\GuzzleClientAdapter;
use Arikaim\Core\Utils\Utils;

/**
 * Http client 
 */
class HttpClient implements HttpClientInterface
{ 
    /**
     * Adapter
     *
     * @var HttpClientAdapterInterface
     */
    private $adapter;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(HttpClientAdapterInterface $adapter = null)
    {
        $this->adapter = (empty($adapter) == true) ? new GuzzleClientAdapter() : $adapter; 
    }

    /**
     * Get adapter
     *
     * @return HttpClientAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Fetch url
     *
     * @param string $url
     * @param array $options
     * @return Response|null
     */
    public function fetch($url, $options = [])
    {
        if (Utils::isValidUrl($url) == false) {
            return null;
        }
        $response = $this->adapter->request('GET',$url,$options);

        return (is_object($response) == true) ? $response->getBody() : null;
    }

    /**
     * Create and send an GET request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. 
     *
     * @return ResponseInterface
     */
    public function get($uri, array $options = [])
    {
        return $this->adapter->request('GET',$uri,$options);
    }

    /**
     * Create and send an HEAD request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function head($uri, array $options = [])
    {
        return $this->adapter->request('HEAD',$uri,$options);
    }

    /**
     * Create and send an PUT request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function put($uri, array $options = [])
    {
        return $this->adapter->request('PUT',$uri,$options);
    }

    /**
     * Create and send an DELETE request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function delete($uri, array $options = [])
    {
        return $this->adapter->request('DELETE',$uri,$options);
    }

    /**
     * Create and send an POST request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function post($uri, array $options = [])
    {
        return $this->adapter->request('POST',$uri,$options);
    }

    /**
     * Create and send an OPTIONS request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function options($uri, array $options = [])
    {
        return $this->adapter->request('OPTIONS',$uri,$options);
    }

    /**
     * Create and send an PATCH request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function patch($uri, array $options = [])
    {
        return $this->adapter->request('PATCH',$uri,$options);
    }

     /**
     * Create and send an TRACE request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function trace($uri, array $options = [])
    {
        return $this->adapter->request('TRACE',$uri,$options);
    }
    
    /**
     * Create and send an CONNECT request.
     *
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function connect($uri, array $options = [])
    {
        return $this->adapter->request('CONNECT',$uri,$options);
    }
}
