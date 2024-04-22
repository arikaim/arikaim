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
     * @param string|null $error 
     */
    public function __construct(array $params = [], ?string $error = null) 
    {
        parent::__construct($params,$error);
     
        $this->setDefaultError('NUMBER_NOT_VALID_ERROR');
    }
    
    /**
     * Validate number value 
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value): bool 
    {
        $errors = 0;
        $result = $this->validateType($value,Rule::NUMBER_TYPE);
        if ($result == false) {
            $errors++;
        } 
        $result = $this->validateMinValue($value);
        if ($result == false) {
            $errors++;
        }   
        $result = $this->validateMaxValue($value);
        if ($result == false) {
            $errors++;
        }
        
        return ($errors == 0);
    } 

    /**
     * Return filter type
     *
     * @return mixed
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
    protected function validateMinValue($value): bool
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
    protected function validateMaxValue($value): bool
    {
        if (empty($this->params->get('max')) == false) {           
            return ($value > $this->params['max']) ? false : true;                
        }
        
        return true;
    }
}
