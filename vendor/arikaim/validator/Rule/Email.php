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
 * Email address validation rule
 */
class Email extends Rule
{
    /**
     * Constructor
     *
     */
    public function __construct() 
    {
        parent::__construct([]);
        $this->setError("EMAIL_NOT_VALID_ERROR");
    }

    /**
     * Return filter type
     *
     * @return int
     */
    public function getType()
    {       
        return FILTER_VALIDATE_EMAIL;
    }
}
