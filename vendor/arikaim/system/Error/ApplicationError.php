<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System\Error;

use Slim\Interfaces\ErrorHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Arikaim\Core\System\Error\PhpError;
use Throwable;

/**
 * Application error handler
 */
class ApplicationError extends PhpError implements ErrorHandlerInterface
{  
    /**
     * Response
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param ResponseInterface $response
     * @param ErrorRendererInterface $htmlRenderer
     * @param boolean $displayErrorDetails
     * @param boolean $displayErrorTrace
     */
    public function __construct(ResponseInterface $response, $htmlRenderer = null, $displayErrorDetails = true, $displayErrorTrace = true)
    {
        $this->response = $response->withStatus(400);   
        
        parent::__construct($htmlRenderer,$displayErrorDetails,$displayErrorTrace);
    }

    /**
     * Invoke error handler
     *
     * @param ServerRequestInterface $request   The most recent Request object
     * @param \Exception             $exception  
     * @param bool                   $displayDetails
     * @param bool                   $logErrors
     * @param bool                   $logErrorDetails
     * @return ResponseInterface    
     */
    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        $output = $this->renderError($request,$exception,$displayDetails,$logErrors,$logErrorDetails);
        $this->response->getBody()->write($output);

        return $this->response;
    }
}
