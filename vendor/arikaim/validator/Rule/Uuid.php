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
use Arikaim\Core\Utils\Uuid as UuidUtils;

/**
 *  Uuid validation rule.Check if value is valid uuid.
 */
class Uuid extends Rule
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

        $this->setDefaultError('UUID_NOT_VALID_ERROR');  
    }

    /**
     * Validate value
     *
     * @param string $value
     * @return boolean
     */
    public function validate($value): bool 
    {
        return UuidUtils::isValid($value);         
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
