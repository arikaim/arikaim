<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * Response validator strategy
 */
class ValidatorStrategy implements InvocationStrategyInterface
{
    /**
     * Events dispacher
     *
     * @var EventDispatcherInterfaceype
     */
    private $eventDispatcher; 

    /**
     * System Errors
     *
     * @var SystemErrorInterface
     */
    private $systemErrors;

    /**
     * Constructor
     *
     * @param EventDispatcherInterfaceype $eventDispatcher
     */
    public function __construct($eventDispatcher = null, $systemErrors = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->systemErrors = $systemErrors;
    }

    /**
     * Invoke a route callable with request, response, Validator with rote parameters.
     * 
     * @param array|callable         $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $routeArguments
     *
     * @return mixed
     */
    public function __invoke(callable $callable, ServerRequestInterface $request, ResponseInterface $response, array $routeArguments): ResponseInterface  
    {
        foreach ($routeArguments as $k => $v) {          
            $request = $request->withAttribute($k, $v);
        }
        $body = $request->getParsedBody();
        $body = (is_array($body) == false) ? [] : $body;
        $data = array_merge($routeArguments,$body);
        $validator = new Validator($data,$this->eventDispatcher,$this->systemErrors);

        return $callable($request, $response, $validator, $routeArguments);
    }
}
