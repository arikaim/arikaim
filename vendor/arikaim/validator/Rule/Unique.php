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
 * Unique value rule, Check if value in model table not exists
 */
class Unique extends DbRule
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
        
        $this->setDefaultError('VALUE_EXIST_ERROR');  
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value): bool 
    {           
        $field = $this->params->get('field',null);     
        $exclude = $this->params->get('exclude',null);
   
        $model = $this->model->where($field,'=',$value);
        if (empty($exclude) == false) {
            $model = $model->where($field,'<>',$exclude);
        }

        return ($model->first() === null);      
    } 
}
