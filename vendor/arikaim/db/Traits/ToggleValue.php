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
 * Update boolean database values (true or false)
*/
trait ToggleValue 
{       
    /**
     * Toggle model attribute value
     *
     * @param string $fieldName
     * @param string|integer|null $id
     * @return boolean
     */
    public function toggle(string $fieldName, $id = null): bool
    {
        $id = $id ?? $this->id;
    
        $model = $this->findById($id);
        if ($model == null) {
            return false;
        }
        $value = $model->getAttribute($fieldName);
        $value = ($value == 0) ? 1 : 0;
        $result = $model->update([$fieldName => $value]);  
        
        return ($result !== false);
    }
}
