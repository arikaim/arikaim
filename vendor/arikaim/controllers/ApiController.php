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

use Arikaim\Core\Http\ApiResponse;
use Arikaim\Core\Http\Response;
use Arikaim\Core\Controllers\Controller;

/**
 * Base class for all Api controllers
*/
class ApiController extends Controller
{    
    /**
     * Api response
     *
     * @var ApiResponse
     */
    protected $response;

    /**
     * Model class name
     *
     * @var string
     */
    protected $modelClass;

    /**
     * Constructor
     */
    public function __construct($container) 
    {
        parent::__construct($container);

        $debug = $container->get('config')->get('debug',false);
        $this->response = new ApiResponse($debug,Response::create());  

        // set default validator error callback
        $this->onValidationError(function ($errors) {
            $this->setErrors($errors);
        });

        $this->modelClass = null;
    }

    /**
     * Set model class name
     *
     * @param string $class
     * @return void
     */
    public function setModelClass($class)
    {
        $this->modelClass = $class;
    }

    /**
     * Get model class name
     *     
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Add message to response, first find in messages array if not found display name value as message 
     *
     * @param string $name  
     * @return Response
     */
    public function message($name)
    {
        $message = $this->getMessage($name);
        $message = (empty($message) == true) ? $name : $message;
        
        return $this->response->message($message);       
    }

    /**
     * Set error, first find in messages array if not found display name value as error
     *
     * @param string $name
     * @return Response
     */
    public function error($name)
    {
        $message = $this->getMessage($name);
        if (empty($message) == true) {
            // check for system error
            $message = $this->get('errors')->get($name,null);
        }
        $message = (empty($message) == true) ? $name : $message;
        
        return $this->response->setError($message);
    }

    /**
     * Set response field
     *
     * @param string $name
     * @param mixed $value
     * @return Response
     */
    public function field($name, $value)
    {
        return $this->response->field($name,$value);      
    }

    /**
     * Set response 
     *
     * @param boolean $condition
     * @param array|Closure $data
     * @param string|Closure $error
     * @return mixed
    */
    public function setResponse($condition, $data, $error)
    {
        if (is_string($error) == true) {
            $message = $this->getMessage($error);
            $error = (empty($message) == true) ? $error : $message;
        }
        if (is_string($data) == true) {
            $message = $this->getMessage($data);
            $data = (empty($message) == true) ? $data : $message;
        }
        
        return $this->response->setResponse($condition,$data,$error);
    }

    /**
     * Forward calls to $this->response and run Controller function if exist
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (is_callable([$this->response,$name]) == true) {
            return call_user_func_array([$this->response,$name], $arguments);     
        }
        //
        $callable = [$this,$name . 'Controller'];
        if (method_exists($this,$name . 'Controller') == true) {
            $arg = (isset($arguments[0]) == true) ? $arguments[0] : null;
            $arg1 = (isset($arguments[1]) == true) ? $arguments[1] : null;
            $arg2 = (isset($arguments[2]) == true) ? $arguments[2] : null;

            $callback = function($arguments) use(&$callable,$arg,$arg1,$arg2) {
                $callable($arg,$arg1,$arg2);
                return $this->getResponse();                 
            };
            return $callback($arguments);
        }
    }

    /**
     * Return response 
     *  
     * @param boolean $raw
     * 
     * @return Response
     */
    public function getResponse($raw = false)
    {
        return $this->response->getResponse($raw);
    }

    /**
     * Reguire permission check if current user have permission
     *
     * @param string $name
     * @param mixed $type
     * @return bool
     */
    public function requireAccess($name, $type = null)
    {       
        if ($this->has('access') == false) {
            return false;
        }
        
        if ($this->get('access')->hasAccess($name,$type) == true) {
            return true;
        }
        
        $this->setError($this->get('errors')->getError("AUTH_FAILED"));                        
        Response::emit($this->getResponse()); 

        exit();       
    }
}
