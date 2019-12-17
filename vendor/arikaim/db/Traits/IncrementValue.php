<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Increment field value
*/
trait IncrementValue 
{       
    /**
     * Increment field value
     *
     * @param string|integer $uuid Unique row id or uuid
     * @param string $fieldName Field name
     * @param integer $increment 
     * @return integer
     */
    public function incrementValue($uuid, $fieldName, $increment = 1)
    {        
        $model = (is_string($uuid) == true) ? parent::where('uuid','=',$uuid)->first() : parent::where('id','=',$uuid)->first();
          
        if (is_object($model) == false) {
            return false;
        }
        $value = $model->getAttribute($fieldName);
        $value += $increment;

        $model->setAttribute($fieldName,$value);
        $model->update();   

        return $value;
    }
}
