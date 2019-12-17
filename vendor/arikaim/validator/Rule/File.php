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
     */
    public function __construct() 
    {
        parent::__construct([]);
    }

    /**
     * Validate file data array
     *
     * @param array $value
     * @return boolean
     */
    public function validate($value) 
    { 
        if (is_array($value) == false) {
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
     * @return int
     */
    public function getType()
    {       
        return FILTER_CALLBACK;
    }
}
