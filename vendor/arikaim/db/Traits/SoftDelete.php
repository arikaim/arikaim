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

use Arikaim\Core\Utils\DateTime;

/**
 * Soft delete trait
 *     
 * Custom soft delete column name
 *  protected $softDeleteColumnName = ' column name '
*/
trait SoftDelete 
{    
    /**
     * Default soft delete column mame
     *
     * @var string
     */
    protected static $DEFAULT_SOFT_DELETE_COLUMN = 'date_deleted';
    
    /**
     * Return true if model is deleted
     *
     * @return boolean
     */
    public function isDeleted(): bool
    {
        return ($this->{$this->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN} !== null);
    }

    /**
     * Get delete models count.
     *
     * @return integer
     */
    public function getDeletedCount(): int
    {
        return $this->softDeletedQuery()->count();      
    }

    /**
     * Soft delete model
     *
     * @param integer|string|null string $id
     * @return boolean
     */
    public function softDelete($id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
      
        return (bool)$model->update([
            $model->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN => DateTime::getTimestamp()
        ]);
    }

    /**
     * Restore soft deleted models
     *
     * @param integer|string|null string $id
     * @return boolean
     */
    public function restore($id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);      
        $model->{$model->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN} = null;
        
        return (bool)$model->save();
    }

    /**
     * Restore all soft deleted rows
     *
     * @return boolean
     */
    public function restoreAll(): bool
    {       
        $query = $this->softDeletedQuery();
        
        return (bool)$query->update([
            $this->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN => null
        ]);
    }

    /**
     * Get soft deleted query
     *
     * @return QueryBuilder
     */
    public function softDeletedQuery(): object
    {
        return $this->whereNotNull($this->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN);
    }

    /**
     * Permanently delete all soft deleted models
     *
     * @return boolean
     */
    public function clearDeleted(): bool
    {
        return (bool)$this->softDeletedQuery()->delete();
    }

    /**
     * Get not deleted query
     *
     * @return QueryBuilder
     */
    public function getNotDeletedQuery(): object
    {
        return $this->whereNull($this->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN);
    }

    /**
     * Get not deleted scope
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGetNotDeleted($query)
    {
        return $query->whereNull($this->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN);
    }

    /**
     * Get deleted scope
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGetDeleted($query)
    {
        return $query->whereNotNull($this->softDeleteColumnName ?? static::$DEFAULT_SOFT_DELETE_COLUMN);
    }
}
