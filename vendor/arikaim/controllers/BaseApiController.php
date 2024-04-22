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

use Arikaim\Core\Controllers\Traits\Base\BaseController;
use Arikaim\Core\Controllers\Traits\Base\ApiResponse;
use Arikaim\Core\Controllers\Traits\Base\UserAccess;

/**
 * BaseApiController class
*/
class BaseApiController
{    
    use 
        BaseController,
        UserAccess,
        ApiResponse;     

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
        $this->init();
       
        $this->clearResult(); 
    }
    
    /**
     * Init controller, override this method in child classes
     *
     * @return void
    */
    public function init(): void
    {
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
}
