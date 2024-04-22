<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Controllers;

use Psr\Http\Message\ResponseInterface;
use Arikaim\Core\Controllers\Traits\Base\ApiResponse;

/**
 * FastApiController class
*/
class FastApiController
{    
    use 
        ApiResponse;     

    /**
     * Container
     *
     * @var Container|null
     */
    protected $container = null;

    /**
     * Response
     *
     * @var ResponseInterface|null
     */
    protected $response = null;

    /**
     * Errors list
     *
     * @var array
     */
    protected $errors = []; 

    /**
     * Constructor
     *
     * @param Container|null $container
     */
    public function __construct($container = null) 
    {
        $this->container = $container;
        $this->clearResult(); 
    }

    /**
     * Add error
     *
     * @param string $message
     * @return void
    */
    public function addError(string $message): void
    {
        $this->errors[] = $message;    
    }

    /**
     * Return true if response have error
     *
     * @return boolean
     */
    public function hasError(): bool 
    {    
        return (\count($this->errors) > 0);         
    }

    /**
     * Set http response instance
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function setHttpResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }
}
