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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use Arikaim\Core\Http\Interfaces\HttpClientAdapterInterface;

/**
 * Http client 
 */
class GuzzleClientAdapter implements HttpClientAdapterInterface
{ 
    /**
     * Http client
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Create and send an HTTP request.
     *
     * @param string              $method  HTTP method.
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. 
     *
     * @return ResponseInterface
    */
    public function request($method, $uri, array $options = [])
    {
        try {
            $response = $this->client->request($method,$uri,$options);
        }
        catch (BadResponseException $e) {
            $response = $e->getResponse();           
        }

        return $response;
    }
}
