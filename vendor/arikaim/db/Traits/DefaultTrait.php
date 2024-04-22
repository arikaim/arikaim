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
    public function getDefaultColumnName(): string
    {
        return $this->defaultColumnName ?? 'default';
    }

    /**
     * Get default key column name
     *
     * @return string
     */
    public function getDefaultKeyColumnName(): string
    {
        return $this->defaultKeyColumnName ?? 'user_id';
    }

    /**
     * Mutator (get) for default attribute.
     *
     * @return bool
     */
    public function getDefaultAttribute()
    {       
        return ($this->attributes[$this->getDefaultColumnName()] == 1);
    }

    /**
     * Set model as default
     *
     * @param integer|string|null $id
     * @param integer|null $keyValue
     * @return bool
     */
    public function setDefault($id = null, $keyValue = null): bool
    {
        $column = $this->getDefaultColumnName();
        $keyColumn = $this->defaultKeyColumnName ?? 'user_id';

        $id = $id ?? $this->id;
        $idColumn = (\is_numeric($id) == true) ? 'id' : 'uuid';

        $models = (empty($keyValue) == false) ? $this->where($keyColumn,'=',$keyValue) : $this->where($idColumn,'<>',$id);
        $models->update([$column => null]);
              
        $model = $this->findById($id);      
        $model->$column = 1;

        return (bool)$model->save();           
    }

    /**
     * Get default model
     *
     * @param mixed|null $keyValue
     * @return Model|null
     */
    public function getDefault($keyValue = null): ?object
    {      
        return $this->defaultQuery($keyValue)->first();
    }

    /**
     * Default scope
     *
     * @param Builder $query
     * @param mixed|null $keyValue
     * @return Builder
     */
    public function scopeDefaultQuery($query, $keyValue = null)
    {
        $query->where($this->getDefaultColumnName(),'=','1');
        $keyColumn = $this->defaultKeyColumnName ?? 'user_id';

        return (empty($userId) == false) ? $query->where($keyColumn,'=',$keyValue) : $query;
    }

    /**
     * Return true if default value is set 
     *
     * @param mixed|null $keyValue
     * @return boolean
     */
    public function hasDefault($keyValue = null): bool
    {
        return ($this->getDefault($keyValue) != null);
    }
}
