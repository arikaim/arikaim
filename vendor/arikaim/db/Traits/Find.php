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
     * @return Model|null
     */
    public function findById($id): ?object
    {         
        return (empty($id) == true) ? null : $this->findByIdQuery($id)->first();
    }
    
    /**
     * Find multiole query
     *
     * @param array $idList
     * @return Builder
     */
    public function findMultiple(array $idList): object
    {
        return $this
            ->whereIn((string)$this->uuidColumnName ?? 'uuid',$idList)
            ->orWhereIn((string)$this->getKeyName(),$idList);
    }

    /**
     * Get last row
     *
     * @param string $field
     * @return Model|null
     */
    public function getLastRow(string $field = 'id'): ?object
    {
        return $this->latest($field)->first();
    }

    /**
     * Get last id
     *
     * @return integer|null
     */
    public function getLastId(): ?int
    {
        $model = $this->getLastRow();

        return ($model != null) ? $model->id : null;
    }

    /**
     * Find model by column name
     *
     * @param mixed $value
     * @param string|null|array $column
     * @return Model|null
     */
    public function findByColumn($value, $column = null): ?object
    {
        return $this->findQuery($value,$column)->first(); 
    }

    /**
     * Return query builder
     *
     * @param mixed $value
     * @param string|null|array $column
     * @return Builder
     */
    public function findQuery($value, $column = null): object
    {      
        if (empty($column) == true) {
            return $this->findByIdQuery($value);
        }

        if (\is_string($column) == true) {
            return $this->where($column,'=',$value);
        }

        if (\is_array($column) == true) {
            $model = $this;
            foreach ($column as $item) {
                if (empty($item) == true) continue;
                $model = $model->orWhere($item,'=',$value);
            }
            return $model;
        }

        return $this->findByIdQuery($value);
    }

    /**
     *  Return query builder
     *
     * @param integer|string $id
     * @return Builder
     */
    public function findByIdQuery($id): object
    {       
        return $this->where($this->getIdAttributeName($id),'=',$id);
    }

    /**
     * Return id column name dependiv of id value type for string return uuid
     *
     * @param integer|string $id
     * @return string
     */
    public function getIdAttributeName($id): string
    {
        return (\is_numeric($id) == true) ? (string)$this->getKeyName() : (string)($this->uuidColumnName ?? 'uuid');
    }

    /**
     * Find collection of models by id or uuid
     *
     * @param array|null $items
     * @return QueryBuilder|false
     */
    public function findItems(?array $items) 
    {
        return (empty($items) == true) ? false : $this->whereIn($this->getIdAttributeName($items[0]),$items);      
    }

    /**
     * Where case insensitive
     *
     * @param string $attribute
     * @param mixed $value
     * @param string $operator
     * @return Builder
     */
    public function whereIgnoreCase(string $attribute, $value, string $operator = '='): object
    {
        return $this->whereRaw('LOWER(' . $attribute .') ' . $operator . ' ?',[\strtolower($value)]);
    }

    /**
     * Case insensitive search
     *
     * @param Builder     $query
     * @param string      $column
     * @param string|null $value
     * @return Builder
     */
    public function scopeSearchIgnoreCase($query, string $column, ?string $value)
    {
        return $query->whereRaw('LOWER(' . $column .') LIKE ' . ' ?',['%' . \strtolower($value ?? '') . '%']);
    }

    /**
     * Return true if atr exist
     *
     * @param string $attr
     * @return boolean
     */
    public function hasAttribute(string $attr): bool
    {
        return \array_key_exists($attr,$this->attributes);
    }
}
