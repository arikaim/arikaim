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
     * Get default column name
     *
     * @return string
     */
    public function getDefaultColumnName()
    {
        return (isset($this->defaultColumnName) == true) ? $this->defaultColumnName : 'default';
    }

    /**
     * Mutator (get) for default attribute.
     *
     * @return array
     */
    public function getDefaultAttribute()
    {       
        $column = $this->getDefaultColumnName();

        return ($this->attributes[$column] == 1);
    }

    /**
     * Set model as default
     *
     * @param integer|string|null $id
     * @return bool
     */
    public function setDefault($id = null)
    {
        $column = $this->getDefaultColumnName();

        $id = (empty($id) == true) ? $this->id : $id;
        $models = $this->where('id','<>',$id);    
       
        $models->update([$column => null]);
        $model = $this->findById($id);
        $model->$column = 1;
       
        return $model->save();               
    }

    /**
     * Get default model
     *
     * @return Model|null
     */
    public function getDefault()
    {
        $column = $this->getDefaultColumnName();
        $model = $this->where($column,'=','1')->first();

        return (is_object($model) == true) ? $model : null; 
    }
}
