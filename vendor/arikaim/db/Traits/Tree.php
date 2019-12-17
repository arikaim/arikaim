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
 * Manage models with parent - child relations.
 *  Change parrent id column name in model:
 *       protected $parentColumn = "column name";
*/
trait Tree 
{       
    /**
     * Get model tree
     *
     * @param Moldel $model
     * @return array
     */
    public function getModelPath($model)
    {
        $result = [];
        array_unshift($result,$model->toArray());
      
        while ($model != false) {
            $parentId = $model->attributes[$this->getParentColumn()];
            $model = parent::where('id','=',$parentId)->first();
            if (is_object($model) == true) {
                array_unshift($result,$model->toArray());
            }
        }

        return $result;
    }

    /**
     * Get parent id attribute name default: parent_id
     *
     * @return string
     */
    public function getParentColumn()
    {
        return (isset($this->parentColumn) == true) ? $this->parentColumn : 'parent_id';
    }

    /**
     * Gte model tree for current model
     *
     * @return void
     */
    public function getTreePath()
    {      
        return $this->getModelPath($this);
    }

    /**
     * Return true if model item have child items
     *
     * @param integer $id
     * @return boolean
     */
    public function hasChild($id = null)
    {
        $id = (empty($id) == true) ? $this->id : $id;
        $columnName = $this->getParentColumn();

        $model = $this->findByColumn($this->id,$columnName);
        if (is_object($model) == true) {
            return ($model->count() > 0) ? true : false; 
        }

        return false;
    }
}
