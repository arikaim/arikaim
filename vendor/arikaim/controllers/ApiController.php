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
use Arikaim\Core\Controllers\Traits\Base\Errors;
use Arikaim\Core\Controllers\Traits\Base\Multilanguage;
use Arikaim\Core\Controllers\Traits\Base\UserAccess;
use Arikaim\Core\Controllers\Traits\Base\ApiResponse;
use Exception;

/**
 * Base class for all Api controllers
*/
class ApiController
{    
    use 
        Multilanguage,
        BaseController,
        UserAccess,
        ApiResponse,
        Errors;

    /**
     * Model class name
     *
     * @var string
     */
    protected $modelClass = null;

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct($container = null) 
    {
        $this->container = $container;
        $this->init();
       
        // set default validator error callback
        $this->onValidationError(function($errors) {
            $errors = $this->resolveValidationErrors($errors);
            $this->setErrors($errors);
            return $errors;
        });

        $this->clearResult(); 
    }
    
    /**
     * Run {method name}Controller function if exist
     *
     * @param string $name
     * @param array $arguments
     * @throws Exception
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $name .= 'Controller';
        if (\method_exists($this,$name) == true) {
            $this->resolveRouteParams($arguments[0]);
            ([$this,$name])($arguments[0],$arguments[1],$arguments[2]);

            return $this->getResponse();
        }

        throw new Exception('Route controller method not found. (' . $name . ')',1);
    }

    /**
     * Init controller, override this method in child classes
     *
     * @return void
    */
    public function init()
    {
    }

    /**
     * Dispatch event
     *
     * @param string $eventName
     * @param array $params
     * @return mixed|false
     */
    public function dispatch(string $eventName, $params) 
    {
        return ($this->has('event') == true) ? $this->get('event')->dispatch($eventName,$params) : false;  
    }

    /**
     * Set model class name
     *
     * @param string $class
     * @return void
     */
    public function setModelClass(string $class): void
    {
        $this->modelClass = $class;
    }

    /**
     * Get model class name
     *     
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }
}
