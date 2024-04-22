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

use Arikaim\Core\Validator\Rule\DbRule;

/**
 * Check if value exists in database table
 */
class Exists extends DbRule
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

        $this->setDefaultError('VALUE_NOT_EXIST_ERROR');
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value): bool 
    {           
        if ($this->model == null) {
            return false;
        }
        
        return (bool)$this->model->where($this->params->get('field'),'=',$value)->exists();
    }    
}
