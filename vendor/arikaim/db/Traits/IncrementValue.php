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
     * @param string $fieldName field name
     * @param integer $increment 
     * @return integer|false
     */
    public function incrementValue($uuid, string $fieldName, int $increment = 1)
    {        
        $model = (\is_string($uuid) == true) ? parent::where('uuid','=',$uuid)->first() : parent::where('id','=',$uuid)->first();
        if ($model == null) {
            return false;
        }
        
        $value = $model->getAttribute($fieldName);
        $value += $increment;

        $model->setAttribute($fieldName,$value);
        $model->update();   

        return $value;
    }
}
