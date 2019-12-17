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
 * Default column trait
*/
trait DefaultTrait 
{        
    /**
     * Mutator (get) for default attribute.
     *
     * @return array
     */
    public function getDefaultAttribute()
    {       
        return ($this->attributes['default'] == 1);
    }

    /**
     * Set model as default
     *
     * @param integer|string|null $id
     * @return bool
     */
    public function setDefault($id = null)
    {
        $id = (empty($id) == true) ? $this->id : $id;
        $models = $this->where('id','<>',$id);    
        $models->update(['default' => null]);

        $model = $this->findById($id);
        $model->default = 1;

        return $model->save();               
    }

    /**
     * Get default model
     *
     * @return Model|null
     */
    public function getDefault()
    {
        $model = $this->where('default','=','1')->first();

        return (is_object($model) == true) ? $model : null; 
    }
}
