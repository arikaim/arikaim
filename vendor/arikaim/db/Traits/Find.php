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
 * Find model
*/
trait Find 
{    
    /**
     * Find model by id or uuid
     *
     * @param integer|string $id
     * @return object|false
     */
    public function findById($id)
    {        
        return $this->findByColumn($id);
    }
    
    /**
     * Find model by column name
     *
     * @param mxied $value
     * @param string $column
     * @return object|false
     */
    public function findByColumn($value, $column = null)
    {
        $model = $this->findQuery($value,$column);
        
        return (is_object($model) == false) ? false : $model->first();
    }

    /**
     * Return query builder
     *
     * @param mixed $value
     * @param string|null|array $column
     * @return object|null
     */
    public function findQuery($value, $column = null)
    {      
        if ($column == null) {
            return $this->findByIdQuery($value);
        }

        if (is_string($column) == true) {
            return parent::where($column,'=',$value);
        }

        if (is_array($column) == true) {
            $model = $this;
            foreach ($column as $item) {
               $model = $model->orWhere($item,'=',$value);
            }
            return $model;
        }

        return null;
    }

    /**
     *  Return query builder
     *
     * @param integer|string $id
     * @return object
     */
    public function findByIdQuery($id)
    {       
        return parent::where($this->getIdAttributeName($id),'=',$id);
    }

    /**
     * Return id column name dependiv of id value type for string return uuid
     *
     * @param integer|string $id
     * @return void
     */
    public function getIdAttributeName($id)
    {
        $uuidAttribute = (method_exists($this,'getUuidAttributeName') == true) ? $this->getUuidAttributeName() : 'uuid';
        return (is_numeric($id) == true) ? $this->getKeyName() : $uuidAttribute;
    }

    /**
     * Find collection of models by id or uuid
     *
     * @param array $items
     * @return QueryBuilder
     */
    public function findItems($items) 
    {
        return (empty($items) == true) ? false : parent::whereIn($this->getIdAttributeName($items[0]),$items);      
    }

    /**
     * Where case insensitive
     *
     * @param string $attribute
     * @param mixed $value
     * @param string $operator
     * @return Query
     */
    public function whereIgnoreCase($attribute, $value, $operator = '=')
    {
        $value = \strtolower($value);
        
        return $this->whereRaw('LOWER(' . $attribute .') ' . $operator . ' ?',[$value]);
    }
}
