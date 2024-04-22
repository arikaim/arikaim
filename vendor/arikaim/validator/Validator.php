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

use Arikaim\Core\Validator\Interfaces\FilterInterface;
use Arikaim\Core\Collection\Collection;
use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Validator\RuleBuilder;
use Arikaim\Core\Validator\DataValidatorException;
use Closure;

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
    private $rules = [];
    
    /**
     * Filters
     *
     * @var array
     */
    private $filters = [];

    /**
     * Validation errors
     *
     * @var array
     */
    private $errors = [];

    /**
     * On valid callback
     *
     * @var Closure|null
     */
    private $onValidCallback = null;

    /**
     * On error callback
     *
     * @var Closure|null
     */
    private $onErrorCallback = null;

    /**
     * Get valida callback
     *
     * @var Closure|null
    */
    private $getValidCallback = null;
    
    /**
     * Get error callback
     *
     * @var Closure|null
     */
    private $getErrorCallback = null;

    /**
     * Constructor
     * 
     * @param array $data
     * @param Closure|null $getValidCallback
     * @param Closure|null $getErrorCallback    
     */
    public function __construct(array $data = [], ?Closure $getValidCallback = null, ?Closure $getErrorCallback = null) 
    {
        parent::__construct($data);
        
        $this->rules = [];
        $this->errors = [];
        $this->filters = [];
        $this->getValidCallback = $getValidCallback;
        $this->getErrorCallback = $getErrorCallback;
    }

    /**
     * Init callback
     *
     * @return void
     */
    protected function initCallback(): void
    {
        if (empty($this->onValidCallback) == true) {
            $this->onValidCallback = ($this->getValidCallback instanceof Closure) ? ($this->getValidCallback)() : null;
        }

        if (empty($this->onErrorCallback) == true) {
            $this->onErrorCallback = ($this->getErrorCallback instanceof Closure) ? ($this->getErrorCallback)() : null;
        }
    }

    /**
     * Set callback for validation done
     *
     * @param Closure $callback
     * @return void
    */
    public function onValid(Closure $callback): void
    {
        $this->onValidCallback = $callback; 
    }

    /**
     * Set callback for error valdation
     *
     * @param Closure $callback
     * @return void
    */
    public function onError(Closure $callback): void
    {
        $this->onErrorCallback = $callback; 
    }

    /**
     * Add validation rule
     *
     * @param Arikaim\Core\Validator\Interfaces\RuleInterface|string $rule
     * @param string|null $fieldName    
     * @param string|null $fieldName
     * @param string|null $errorCode
     * @return Validator
     */
    public function addRule($rule, ?string $fieldName = null, ?string $errorCode = null) 
    {                
        if (\is_string($rule) == true) {
            $rule = RuleBuilder::createRule($rule,$errorCode);
        }
     
        if ($rule !== null) {      
            $fieldName = (empty($fieldName) == true) ? '*' : $fieldName;
            if (\array_key_exists($fieldName,$this->rules) == false) {
                $this->rules[$fieldName] = [];
            }
            $this->rules[$fieldName][] = $rule;                   
        } 

        return $this;
    }

    /**
     * Return filter builder
     *
     * @return Arikaim\Core\Validator\FilterBuilder
     */
    public function filter()
    {
        return new \Arikaim\Core\Validator\FilterBuilder();
    }    

    /**
     * Add filter
     *
     * @param string|null $fieldName
     * @param Filter|string $filter
     * @param array $args
     * @return Validator
     */
    public function addFilter(?string $fieldName, $filter, array $args = []) 
    {                   
        $fieldName = (empty($fieldName) == true) ? '*' : $fieldName;
        if (\is_string($filter) == true) {
            $filter = Factory::createInstance(Factory::getValidatorFiltersClass($filter),$args);                   
        }
       
        if ($filter instanceof FilterInterface) {
            if (\array_key_exists($fieldName,$this->filters) == false) {
                $this->filters[$fieldName] = [];
            }    
            $this->filters[$fieldName][] = $filter;    
        }
                                                 
        return $this;
    }
    
    /**
     * Sanitize form fields values
     *
     * @param array|null $data
     * @return Validator
     */
    public function doFilter(?array $data = null) 
    {         
        if (empty($data) == false) {
            $this->data = $data;
        }
      
        foreach ($this->data as $fieldName => $value) {            
            $filters = $this->getFilters($fieldName);            
            foreach ($filters as $filter) {
                $this->data[$fieldName] = $filter->processFilter($this->data[$fieldName]);
            }                 
        }      
      
        return $this;
    }

    /**
     * Sanitize and validate form
     *
     * @param bool $throwException
     * @return bool
     */
    public function filterAndValidate(bool $throwException = false): bool
    {
        return $this->doFilter()->validate($throwException);
    }

    /**
     * Validate rules
     *
     * @param string $fieldName
     * @param array $rules
     * @return bool
     */
    public function validateRules(string $fieldName, array $rules): bool
    {
        $value = $this->get($fieldName,null);
        $errors = 0;
        
        foreach ($rules as $rule) {    
            if ($this->validateRule($rule,$value) == false) {  
                $this->addError($fieldName,$rule->getError(),$rule->getErrorParams()); 
                $errors++;              
            }
        }

        return ($errors == 0);
    }

    /**
     * Validate rule
     *
     * @param Arikaim\Core\Validator\Interfaces\RuleInterface $rule
     * @param mixed $value
     * @return bool
     */
    public function validateRule($rule, $value): bool
    {  
        if (empty($value) == true) {
            return ($rule->isRequired() == true) ? false : true;
        }

        $type = $rule->getType();
        $ruleOptions = ($type == FILTER_CALLBACK) ? ['options' => [$rule, 'validate']] : [];    

        return (bool)\filter_var($value,$type,$ruleOptions);        
    }

    /**
     * Validate 
     *
     * @param bool $throwException
     * @return boolean
     * @throws DataValidatorException
     */
    public function validate(bool $throwException = false): bool
    {
        $this->errors = [];   
        $this->initCallback();

        // do filters
        $this->doFilter();

        foreach ($this->rules as $fieldName => $rules) {  
            $this->validateRules($fieldName,$rules);
        }
       
        if ($this->isValid() == true) {
            // run data valid callback
            if ($this->onValidCallback instanceof Closure) {
                ($this->onValidCallback)($this);  
            }  

            return true;
        }

        // run error callback       
        if ($this->onErrorCallback instanceof Closure) {
            $errors = ($this->onErrorCallback)($this->getErrors()); 
        }                      
        
        if ($throwException == true) {
            throw new DataValidatorException($errors ?? $this->getErrors(),'Data validation error');
        }

        return false;   
    }

    /**
     * Set validation error
     *
     * @param string $fieldName
     * @param string|null $errorCode
     * @param array $params
     * @return void
     */
    public function addError(string $fieldName, ?string $errorCode, array $params = []): void
    {
        $error = [
            'field_name' => $fieldName,
            'error_code' => $errorCode,
            'params'     => $params
        ];
        $this->errors[] = $error;
    }

    /**
     * Sanitize form value
     *
     * @param mixed $value
     * @param int $type
     * @return mixed
     */
    public static function sanitizeVariable($value, $type = FILTER_SANITIZE_STRING) 
    {     
        return \filter_var(\trim($value ?? ''),$type);       
    }

    /**
     * Return true if form is valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return (\count($this->errors) == 0);     
    }

    /**
     * Return validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return number of errors
     *
     * @return int
     */
    public function getErrorsCount(): int
    {
        return \count($this->errors);
    }

    /**
     * Return validation rules
     *
     * @param string $fieldName
     * @return array
     */
    public function getRules(string $fieldName): array
    {
        return $this->rules[$fieldName] ?? [];          
    }

    /**
     * Return form filters
     *
     * @param string $fieldName
     * @return array
     */
    public function getFilters($fieldName)
    {   
        $all = $this->filters['*'] ?? [];

        return (isset($this->filters[$fieldName]) == true) ? \array_merge($all,$this->filters[$fieldName]) : $all;          
    }

    /**
     * Get value from collection
     *
     * @param string $key Name
     * @param mixed $default If key not exists return default value
     * @return mixed
     */
    public function get(string $key, $default = null)
    {       
        $item = $this->data[$key] ?? $default;
        if (\is_array($item) == true) {
            return $item;
        }
        if (\is_string($item) == true) {
            return \trim($item ?? '');
        }

        return $item;        
    }

    /**
     * Get item 
     *
     * @param mixed $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Get field converted to timestamp
     *
     * @param string       $key
     * @param mixed        $default
     * @param integer|null $baseTimestamp
     * @return integer|null
     */
    public function getTimestamp(string $key, $default = null, ?int $baseTimestamp = null): ?int
    {
        $date = $this->get($key,$default);
        if (empty($date) == true) {
            return $default;
        }

        return (\is_numeric($date) == true) ? $date : \strtotime((string)$date,$baseTimestamp);
    }

    /**
     * Convert filed to timestamp
     *
     * @param string       $key
     * @param mixed       $default
     * @param integer|null $baseTimestamp
     * @return Self
     */
    public function toTimeStamp(string $key, $default = null, ?int $baseTimestamp = null): object
    {
        $this->data[$key] = $this->getTimestamp($key,$default,$baseTimestamp);

        return $this;
    }
}
