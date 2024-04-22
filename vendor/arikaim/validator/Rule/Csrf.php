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
     * @param string|null $error 
    */
    public function __construct(array $params = [], ?string $error = null) 
    {
        parent::__construct($params,$error);
        
        $this->required();
        $this->setDefaultError('ACCESS_DENIED');
    }

    /**
     * Verify if value is valid
     *
     * @param string $value
     * @return boolean
     */
    public function validate($value): bool 
    {
        return CsrfToken::validateToken($value);      
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
