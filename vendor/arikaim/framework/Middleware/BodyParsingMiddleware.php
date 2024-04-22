<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Arikaim\Core\Framework\Middleware\Middleware;
use Arikaim\Core\Framework\MiddlewareInterface;
use RuntimeException;

/**
 * Request body parsing
 */
class BodyParsingMiddleware extends Middleware implements MiddlewareInterface
{
    /**
     * @var callable[]
     */
    protected $parsers;

    /**
     * Process middleware 
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return array [$request,$response]
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): array
    {          
        if (empty($request->getParsedBody()) == true) {
            $this->registerDefaultBodyParsers();
            $parsedBody = $this->parseBody($request);
            $request = $request->withParsedBody($parsedBody);
        }

        // Add Content-Length header if not already added
        $size = $response->getBody()->getSize();
        if ($size > 0 && $response->hasHeader('Content-Length') == false) {
            $response = $response->withHeader('Content-Length',(string)$size);
        }

        return [$request,$response];
    }

    /**
     * Register parser 
     * 
     * @param string   $mediaType 
     * @param callable $callable  
     * @return void
     */
    public function registerBodyParser(string $mediaType, callable $callable): void
    {
        $this->parsers[$mediaType] = $callable;       
    }

    /**
     * Return true if parser exist
     * 
     * @param string   $mediaType 
     * @return boolean
     */
    public function hasBodyParser(string $mediaType): bool
    {
        return isset($this->parsers[$mediaType]);
    }

    /**
     * Get parser
     * 
     * @param string    $mediaType 
     * @return callable|null
     * @throws RuntimeException
     */
    public function getParser(string $mediaType): ?callable
    {
        return $this->parsers[$mediaType] ?? null;
    }

    /**
     * Register default parsers
     *
     * @return void
     */
    protected function registerDefaultBodyParsers(): void
    {
        // json      
        $this->parsers = [
            'application/json'                  => function($input) {
                $result = \json_decode($input,true);
                return (\is_array($result) == false) ? null : $result;    
            },
            'application/x-www-form-urlencoded' => function($input) {
                \parse_str($input,$data);
                return $data;
            },
            'application/xml'                   => function($input) {          
                $result = \simplexml_load_string($input);
                \libxml_clear_errors();
                \libxml_use_internal_errors(true);
    
                return ($result === false) ? null : $result;             
            }     
        ];    
    }

    /**
     * Parse body
     * 
     * @param ServerRequestInterface $request
     * @return null|array|object
     */
    protected function parseBody(ServerRequestInterface $request)
    {
        $mediaType = $this->getMediaType($request);
        if ($mediaType === null) {
            return null;
        }

        return (isset($this->parsers[$mediaType]) == true) ? $this->parsers[$mediaType]((string)$request->getBody()) : null;
    }

    /**
     * Get media type
     * 
     * @param ServerRequestInterface $request
     * @return string|null 
     */
    protected function getMediaType(ServerRequestInterface $request): ?string
    {
        $contentType = \trim($request->getHeader('Content-Type')[0] ?? '');

        return (empty($contentType) == false) ? \strtolower( \trim(\explode(';',$contentType)[0]) ) : null;        
    }
}
