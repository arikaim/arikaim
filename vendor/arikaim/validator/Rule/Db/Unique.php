<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\Validator\Rule\Db;

use Arikaim\Core\Validator\Rule\Db\DbRule;

/**
 * Unique value rule, Check if value in model table not exists
 */
class Unique extends DbRule
{
    /**
     * Constructor
     * params model,field, extension, exclude
     * @param array $params
     */
    public function __construct($params) 
    {
        parent::__construct($params);
        $this->setError("VALUE_EXIST_ERROR");  
    }

    /**
     * Validate value
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value) 
    {           
        $field = $this->params->get('field',null);     
        $exclude = $this->params->get('exclude',null);
   
        $model = $this->model->where($field,'=',$value);
        if (empty($exclude) == false) {
            $model = $model->where($field,'<>',$exclude);
        }

        return ($model->first() === null) ? true : false;          
    } 
}
