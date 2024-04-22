<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator\Rule;

use Arikaim\Core\Validator\Rule;

/**
 * Regexp validation rule
 */
class Regexp extends Rule
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

        $this->setDefaultError('REGEXP_NOT_VALID_ERROR');
    }
    
    /**
     * Validate regexp value 
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value): bool 
    {
        $exp = $this->params->get('exp');
        $exp = (\is_array($exp) == true) ? $exp[0] : $exp;
           
        return \preg_match($exp,$value);
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
