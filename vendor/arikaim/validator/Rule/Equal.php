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
 * Equal validation rule. 
 */
class Equal extends Rule
{    
    /**
     * Constructor
     * params - value
     * @param array $params
     */
    public function __construct($params) 
    {
        parent::__construct($params);
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) 
    { 
        return ($value != $this->params->get('value')) ? false : true;
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
