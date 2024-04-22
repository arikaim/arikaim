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

use Psr\Http\Message\ResponseInterface;

use Arikaim\Core\Interfaces\HttpClientInterface;
use Arikaim\Core\Http\Interfaces\HttpClientAdapterInterface;
use Arikaim\Core\Http\GuzzleClientAdapter;
use Arikaim\Core\Http\ApiResponse;
use Exception;

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
     */
    public function __construct(?HttpClientAdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?? new GuzzleClientAdapter();
    }

    /**
     * Create and send an http request.
     *
     * @param string $method
     * @param string|UriInterface $uri URI object or string.
     * @param array $options Request options to apply.
     *
     * @return ResponseInterface
    */
    public function request(string $method, $uri, array $options = [])
    {
        return $this->adapter->request($method,$uri,$options);
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
     * @param string|UriInterface $url
     * @param array $options
     * @return mixed|null
     */
    public function fetch($url, array $options = [])
    {       
        $response = $this->adapter->request('GET',$url,$options);

        return (\is_object($response) == true) ? $response->getBody() : null;
    }

    /**
     * Create and send an GET request.
     *
     * @param string|UriInterface $uri URI object or string.
     * @param array $options Request options to apply. 
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
     * @param array $options Request options to apply.
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

    /**
     * Convert reponse to array
     *
     * @param mixed $response
     * @return array|null
     * @throws Exception
     */
    public function toArray($response): ?array
    {
        if ($response instanceof ResponseInterface) {
            return (new ApiResponse($response))->toArray();
        }

        if (\is_string($response) == true) {
            return \json_decode($response,true);
        }

        if (\is_object($response) == true) {
            return (array)$response;
        }

        if (\is_array($response) == true) {
            return $response;
        }

        throw new Exception('Not valid response');
        return null;
    }
}
