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
use Arikaim\Core\Access\Csrf as CsrfToken;

/**
 * Csrf token field rule 
 */
class Csrf extends Rule
{    
    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct($params = []) 
    {
        parent::__construct($params);
        $this->required();
        $this->setError("ACCESS_DENIED");
    }

    /**
     * Verify if value is valid
     *
     * @param string $value
     * @return boolean
     */
    public function validate($value) 
    {
        return CsrfToken::validateToken($value);      
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
