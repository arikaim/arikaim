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
 * Check if field value is valid file array 
 */
class File extends Rule
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
    }

    /**
     * Validate file data array
     *
     * @param array $value
     * @return boolean
     */
    public function validate($value): bool 
    { 
        if (\is_array($value) == false) {
            return false;
        }
        if (isset($value['data']) == false) {
            // missing data 
            return false;
        }
        if (isset($value['name']) == false) {
            // missing name 
            return false;
        }
        
        return true;
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
