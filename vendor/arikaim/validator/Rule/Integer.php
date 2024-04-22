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

use Arikaim\Core\Validator\Rule\Number;
use Arikaim\Core\Validator\Rule;

/**
 * Integer rule validation
 */
class Integer extends Number
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

        $this->setDefaultError('INT_NOT_VALID_ERROR');
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return void
     */
    public function validate($value): bool 
    {       
        $errors = 0;
        $result = $this->validateType($value,Rule::INTEGER_TYPE);
        if ($result == false) {
            $this->setError('INT_NOT_VALID_ERROR');
            $errors++;
        } 
        $result = $this->validateMinValue($value);
        if ($result == false) {
            $this->setError('NUMBER_MIN_VALUE_ERROR');
            $errors++;
        }   
        $result = $this->validateMaxValue($value);
        if ($result == false) {
            $this->setError('NUMBER_MAX_VALUE_ERROR');
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
}
