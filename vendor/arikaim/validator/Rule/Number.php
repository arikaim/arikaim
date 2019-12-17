<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator\Rule;

use Arikaim\Core\Validator\Rule;

/**
 * Number form rule validation
 */
class Number extends Rule
{
    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct($params = []) 
    {
        parent::__construct($params);

        $this->setError("NUMBER_NOT_VALID_ERROR");
    }
    
    /**
     * Validate number value 
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) 
    {
        $errors = 0;
        $result = $this->validateType($value,Rule::NUMBER_TYPE);
        if ($result == false) {
            $this->setError("NUMBER_NOT_VALID_ERROR");
            $errors++;
        } 
        $result = $this->validateMinValue($value);
        if ($result == false) {
            $this->setError("NUMBER_MIN_VALUE_ERROR");
            $errors++;
        }   
        $result = $this->validateMaxValue($value);
        if ($result == false) {
            $this->setError("NUMBER_MAX_VALUE_ERROR");
            $errors++;
        }
        
        return ($errors > 0) ? false : true;
    } 

    /**
     * Return filter type
     *
     * @return int
     */
    public function getType()
    {       
        return FILTER_CALLBACK;
    }

    /**
     * Minimum field value validation
     *
     * @param int|float $value
     * @return boolean
     */
    protected function validateMinValue($value)
    {
        if (empty($this->params->get('min')) == false) {                 
            return ($value < $this->params['min']) ? false : true; 
        }
        return true;
    }

    /**
     * Maximum field value validation
     *
     * @param int|float $value
     * @return boolean
     */
    protected function validateMaxValue($value)
    {
        if (empty($this->params->get('max')) == false) {           
            return ($value > $this->params['max']) ? false : true;                
        }
        return true;
    }
}
