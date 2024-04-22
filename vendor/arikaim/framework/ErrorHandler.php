<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Arikaim\Core\Http\Request;
use Arikaim\Core\App\Install;
use Arikaim\Core\Routes\RouteType;
use Arikaim\Core\System\Error\ApplicationError;
use Arikaim\Core\System\Error\ErrorHandlerInterface;
use Arikaim\Core\Validator\DataValidatorException;
use Arikaim\Core\Http\ApiResponse;
use ErrorException;
use Throwable;

/**
 * Error handler
 */
class ErrorHandler
{
    /**
     * Container
     *
     * @var ContainerInterface|null
     */
    protected $container = null;

    /**
     * Error renderer
     *
     * @var ErrorHandlerInterface|null
     */
    protected $renderer = null;

    /**
     * Constructor
     *
     * @param ContainerInterface|null $container
     * @param object|null $renderer
     */
    public function __construct(?ContainerInterface $container = null, ?ErrorHandlerInterface $renderer = null)
    {        
        $this->container = $container;    
        $this->renderer = $renderer;            
    }

    /**
     * Handle php app errors
     *
     * @param mixed $num
     * @param mixed $message
     * @param mixed $file
     * @param mixed $line
     * @return void
     */
    public function handleError($num, $message, $file, $line)
    {
        throw new ErrorException($message,0,$num,$file,$line);
    }

    /**
     * Render exception
     *
     * @param Throwable $exception
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function renderExecption(Throwable $exception, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {       
        $uri = $request->getUri()->getPath();
        if (Install::isInstalled() == false) {               
            if (RouteType::isInstallPage() == false && RouteType::isApiInstallRequest($uri) == false) { 
                // redirect to install page     
                return $this->redirectToInstallPage($response);                  
            }                       
        }
    
        // check for redirect url
        if (empty($response->getHeaderLine('Location')) == false) {
            return $response->withStatus(307);
        };

        $this->resolveRenderer();

        // set status code
        $statusCode = ($exception instanceof HttpException) ? $exception->getStatusCode() : 400;
        $response = $response->withStatus($statusCode);

        // validation exception
        if ($exception instanceof DataValidatorException) {
            $apiResponse = new ApiResponse($response);
            $apiResponse->setErrors($exception->getErrors());
            return $apiResponse->getResponse();
        }

        // render errror
        $renderType = (Request::isJsonContentType($request) == true) ? 'json' : 'html';
       
        $output = $this->renderer->renderError($exception,$renderType);
        $response->getBody()->write($output);

        return $response;      
    }

    /**
     * Redirect to install page
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function redirectToInstallPage(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withoutHeader('Cache-Control')
            ->withHeader('Cache-Control','no-cache, must-revalidate')  
            ->withHeader('Expires','Sat, 26 Jul 1997 05:00:00 GMT')        
            ->withHeader('Location',RouteType::getInstallPageUrl())
            ->withStatus(307);   
    }

    /**
     * Create renderer if not set
     *
     * @return void
     */
    private function resolveRenderer(): void
    {
        if ($this->renderer == null) {
            $this->renderer = new ApplicationError($this->container->get('page'));              
        }
    } 
}
