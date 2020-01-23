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
*/
trait SoftDelete 
{    
    /**
     * Return true if model is deleted
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return ! is_null($this->{$this->getDeletedColumn()});
    }

    /**
     * Get delete models count.
     *
     * @return integer
     */
    public function getDeletedCount()
    {
        $query = $this->softDeletedQuery();
        return $query->count();
    }

    /**
     * Soft delete model
     *
     * @param integer string $id
     * @return boolean
     */
    public function softDelete($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $model->{$model->getDeletedColumn()} = DateTime::getTimestamp();
        
        return $model->save();
    }

    /**
     * Restore soft deleted models
     *
     * @param integer|string|null string $id
     * @return boolean
     */
    public function restore($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $model->{$model->getDeletedColumn()} = null;
        
        return $model->save();
    }

    /**
     * Restore all soft deleted rows
     *
     * @return boolean
     */
    public function restoreAll()
    {
        $columName = $this->getDeletedColumn();
        $query = $this->softDeletedQuery();
        
        return $query->update([
            $columName => null
        ]);
    }

    /**
     * Get soft deleted query
     *
     * @return QueryBuilder
     */
    public function softDeletedQuery()
    {
        return $this->whereNotNull($this->getDeletedColumn());
    }

    /**
     * Permanently delete all soft deleted models
     *
     * @return boolean
     */
    public function clearDeleted()
    {
        return $this->softDeletedQuery()->delete();
    }

    /**
     * Get not deleted query
     *
     * @return QueryBuilder
     */
    public function getNotDeletedQuery()
    {
        return parent::whereNull($this->getDeletedColumn());
    }

    /**
     * Get uuid attribute name
     *
     * @return string
     */
    public function getDeletedColumn()
    {
        return (isset($this->softDeleteColumn) == true) ? $this->softDeleteColumn : 'date_deleted';
    }
}
