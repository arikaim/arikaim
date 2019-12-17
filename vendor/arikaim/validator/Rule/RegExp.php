<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Form\Rule;

use Arikaim\Core\Validator\Rule;

/**
 * Reg exp validation rule
 */
class RegExp extends Rule
{   
    /**
     * Constructor
     *
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setError("REGEXP_NOT_VALID_ERROR");
    }
    
    /**
     * Return filter type
     *
     * @return int
     */
    public function getType()
    {       
        return FILTER_VALIDATE_REGEXP;
    }
}
