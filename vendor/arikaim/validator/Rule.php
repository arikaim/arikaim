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

use Arikaim\Core\Validator\Interfaces\RuleInterface;
use Arikaim\Core\Collection\Collection;

/**
 * Base class for all form validation rules
 */
abstract class Rule implements RuleInterface
{    
    const INTEGER_TYPE  = 1;
    const STRING_TYPE   = 2;
    const FLOAT_TYPE    = 3;    
    const BOOLEAN_TYPE  = 4;
    const NUMBER_TYPE   = 5;
    const ITEMS_ARRAY   = 6;

    /**
     * Rule error
     *
     * @var string|null
     */
    protected $error = null;

    /**
     * Default errror code
     *
     * @var string|null
     */
    protected $defaultError = null;

    /**
     * Error params
     *
     * @var array
     */
    protected $errorParams = [];

    /**
     * Rule params
     *
     * @var Collection
     */
    protected $params;

    /**
     * Return rule type
     *
     * @return mixed
     */
    abstract public function getType();
    
    /**
     * Validate rule value callback
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        return false;
    }

    /**
     * Constructor
     *
     * @param string|null $error
     * @param array $params 
     */
    public function __construct(array $params = [], ?string $error = null) 
    {
        $this->params = new Collection($params);  
        $this->errorParams = [];       
        $this->setError($error,'NOT_VALID_VALUE_ERROR');       
    }

    /**
     * Set default error code
     *
     * @param string $errorCode
     * @return void
     */
    public function setDefaultError(string $errorCode): void
    {
        $this->defaultError = $errorCode;
    }

    /**
     * Return rule params
     *
     * @return Collection
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return true if field rule is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->params->get('required',false);
    }

    /**
     * Set rule required
     *
     * @param boolean $value
     * @return void
     */
    public function required($value = true)
    {
        $this->params->set('required',$value);
    }

    /**
     * Validate field type
     *
     * @param mixed $value
     * @param int $type
     * @return bool
     */
    protected function validateType($value, $type)
    {
        switch ($type) {
            case Self::INTEGER_TYPE: {        
                if (\is_numeric($value) == true) {                                   
                    return \is_int((int)$value);
                }
                break;
            }
            case Self::STRING_TYPE: {
                return \is_string($value);         
            }
            case Self::FLOAT_TYPE: {
                if (\is_numeric($value) == true) {                  
                    return \is_float((float)$value);
                }
                break;
            }
            case Self::NUMBER_TYPE: {
                return \is_numeric($value);              
            }
            case Self::ITEMS_ARRAY: {
                return \is_array($value);
            }
            default: {
                return true;
            }
        }       

        return false;
    }

    /**
     * Set validation error ode
     *
     * @param string|null $error
     * @param string|null $default
     * @return void
     */
    public function setError(?string $error, ?string $default = null): void
    {
        $this->error = $error;
        $this->defaultError = $default;
    }

    /**
     * Get error params
     *
     * @return array
     */
    public function getErrorParams(): array
    {
        return \array_merge($this->errorParams,$this->params->toArray());   
    }

    /**
     * Set error params
     *
     * @param array $params
     * @return void
     */
    public function setErrorParams($params = []): void
    {
        $this->errorParams = $params;
    }

    /**
     * Return validation error code
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return (empty($this->error) == true) ? $this->defaultError : $this->error;
    }
}
