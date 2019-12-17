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

use Arikaim\Core\Interfaces\Events\EventDispatcherInterface;
use Arikaim\Core\Interfaces\SystemErrorInterface;
use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Validator\Rule;
use Arikaim\Core\Validator\FilterBuilder;
use Arikaim\Core\Validator\RuleBuilder;

/**
 * Data validation
 */
class Validator extends Collection 
{
    /**
     * validation rules
     *
     * @var array
     */
    private $rules;
    
    /**
     * Filters
     *
     * @var array
     */
    private $filters;

    /**
     * Validation errors
     *
     * @var array
     */
    private $errors;

    /**
     * Callback for valid event
     *
     * @var \Closure
     */
    private $onValid = null;

    /**
     * Callback for validation fail event
     *
     * @var \Closure
     */
    private $onFail = null;

    /**
     * Validate callback
     *
     * @var \Closure
     */
    private $callback;

    /**
     * Event Dispatcher
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * System errors
     *
     * @var SystemErrorInterface
     */
    private $systemErrors;

    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct($data = [], EventDispatcherInterface $eventDispatcher = null, SystemErrorInterface $systemErrors = null) 
    {
        parent::__construct($data);
        
        $this->rules = [];
        $this->errors = [];
        $this->filters = [];
        $this->eventDispatcher = $eventDispatcher;
        $this->systemErrors = $systemErrors;
    }

    /**
     * Add validation rule
     *
     * @param string $fieldName
     * @param Rule|string $rule
     * @param string|null $error
     * 
     * @return Validator
     */
    public function addRule($rule, $fieldName = null, $error = null) 
    {                
        if (is_string($rule) == true) {
            $rule = $this->rule()->createRule($rule,$error);
        }
        if (is_object($rule) == true) {      
            $fieldName = (empty($fieldName) == true) ? $rule->getFieldName() : $fieldName;
            if (array_key_exists($fieldName,$this->rules) == false) {
                $this->rules[$fieldName] = [];
            }
            array_push($this->rules[$fieldName],$rule);  
            return $this;         
        } 

        return $this;
    }

    /**
     * Return rule builder
     *
     * @return RuleBuilder
     */
    public function rule()
    {  
        return new RuleBuilder();
    }

    /**
     * Return filter builder
     *
     * @return FilterBuilder
     */
    public function filter()
    {
        return new FilterBuilder();
    }    

    /**
     * Add filter
     *
     * @param string $fieldName
     * @param Filter $filter
     * @return Validator
     */
    public function addFilter($fieldName, Filter $filter) 
    {                   
        if (is_string($filter) == true) {
            $filter = FilterBuilder::createFilter($fieldName,$filter);
        }

        if ($filter instanceof Filter) {
            if (array_key_exists($fieldName,$this->filters) == false) {
                $this->filters[$fieldName] = [];
            }    
            array_push($this->filters[$fieldName],$filter);               
            return true;
        }           
        return $this;
    }
    
    /**
     * Sanitize form fields values
     *
     * @param array $data
     * @return Validator
     */
    public function doFilter($data = null) 
    {          
        if ($data != null) {
            $this->data = $data;
        }
        foreach ($this->data as $fieldName => $value) {     
            $filters = $this->getFilters($fieldName); 
            foreach ($filters as $filter) {
                if (is_object($filter) == true) {
                    $this->data[$fieldName] = $filter->processFilter($this->data[$fieldName]);
                }
            }                 
        }      

        return $this;
    }

    /**
     * Sanitize and validate form
     *
     * @param array $data
     * @return void
     */
    public function filterAndValidate($data = null)
    {
        return $this->doFilter($data)->validate($data);
    }

    /**
     * Validate rules
     *
     * @param string $fieldName
     * @param array $rules
     * @return bool
     */
    public function validateRules($fieldName, $rules)
    {
        $value = $this->get($fieldName,null);
        $errors = 0;
        foreach ($rules as $rule) {    
            $valid = $this->validateRule($rule,$value);
            if ($valid == false) {
                // ['field_name' => $fieldName]
                $errorMessage = $this->resolveErrorMessage($rule,$fieldName);
                $this->addError($fieldName,$errorMessage); 
                $errors++;              
            }
        }

        return ($errors == 0);
    }

    /**
     * Return error message
     *
     * @param Rule $rule
     * @return string $fieldName
     */
    public function resolveErrorMessage($rule, $fieldName) 
    {
        $errorCode = $rule->getError();
        if (is_object($this->systemErrors) == false) {
            return $errorCode;
        }  
        $params = $rule->getErrorParams();
        $params['field_name'] = $fieldName;

        $errorMessage = $this->systemErrors->getError($errorCode,$params,null);

        return (empty($errorMessage) == true) ? $errorCode : $errorMessage;              
    }

    /**
     * Validate rule
     *
     * @param Rule $rule
     * @param mxied $value
     * @return bool
     */
    public function validateRule($rule, $value)
    {
        if (empty($value) == true && $rule->isRequired() == false) {
            return true;
        }

        $type = $rule->getType();
        $ruleOptions = ($type == FILTER_CALLBACK) ? ['options' => [$rule, 'validate']] : [];
          
        $result = filter_var($value,$type,$ruleOptions); 

        return $result;
    }

    /**
     * Validate 
     *
     * @param array $data
     * @param array $rules
     * @return boolean
     */
    public function validate($data = null, $rules = null)
    {
        $this->errors = [];
        if (is_array($data) == true) {
            $this->data = $data;
        }
        
        if (is_callable($this->callback) == true) {
            $this->callback($this->data);
        }
          
        foreach ($this->rules as $fieldName => $rules) {  
            $this->validateRules($fieldName,$rules);
        }

        $valid = $this->isValid();
        if ($valid == true) {
            // run events callback
            if (is_object($this->eventDispatcher) == true) {
                $this->eventDispatcher->dispatch('validator.valid',$this->data,true);
            }
          
            if (empty($this->onValid) == false) {
                $this->onValid->call($this,$this->data);
            }          
        } else {
            // run events callback
            if (is_object($this->eventDispatcher) == true) {
                $this->eventDispatcher->dispatch('validator.error',$this->getErrors(),true);
            }
            if (empty($this->onFail) == false) {               
                $this->onFail->call($this,$this->getErrors());
            }           
        }

        return $valid;   
    }

    /**
     * Set validator callback
     *
     * @param \Closure $callback
     * @return void
     */
    public function validatorCallback(\Closure $callback)
    {
        $this->callback = function() use($callback) {
            $callback($this->data);
        };
    }

    /**
     * Callback for not valid data
     *
     * @param \Closure $callback
     * @return void
     */
    public function onFail(\Closure $callback)
    {
        $this->onFail = $callback;
    }

    /**
     * Callback for valid data
     *
     * @param \Closure $callback
     * @return void
     */
    public function onValid(\Closure $callback)
    {
        $this->onValid = $callback;
    }

    /**
     * Set validation error
     *
     * @param string $fieldName
     * @param string $message
     * @return void
     */
    public function addError($fieldName, $message)
    {
        $error = [
            'field_name' => $fieldName,
            'message'    => $message
        ];
        array_push($this->errors,$error);
    }

    /**
     * Sanitize form value
     *
     * @param mixed $value
     * @param int $type
     * @return void
     */
    public static function sanitizeVariable($value, $type = FILTER_SANITIZE_STRING) 
    {
        $value = trim($value);
        $value = filter_var($value,$type);

        return $value;
    }

    /**
     * Return true if form is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->getErrorsCount() > 0) ? false : true;          
    }

    /**
     * Return validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return number of errors
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return count($this->errors);
    }

    /**
     * Return validation rules
     *
     * @param string $fieldName
     * @return array
     */
    public function getRules($fieldName)
    {
        return (isset($this->rules[$fieldName]) == true) ? $this->rules[$fieldName] : [];          
    }

    /**
     * Return form filters
     *
     * @param string $fieldName
     * @return array
     */
    public function getFilters($fieldName)
    {   
        $all = (isset($this->filters['*']) == true) ? $this->filters['*'] : [];

        return (isset($this->filters[$fieldName]) == true) ? array_merge($all,$this->filters[$fieldName]) : $all;          
    }
}
