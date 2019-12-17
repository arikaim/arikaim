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
 *  Polymorphic Relations (Many To Many) trait      
*/
trait PolymorphicRelations 
{           
    /**
     * Get relation model class
     *
     * @return string
     */
    public function getRelationModelClass()
    {
        return (isset($this->relationModelClass) == true) ? $this->relationModelClass : null;
    }

    /**
     * Get relation attribute name
     *
     * @return string
     */
    public function getRelationAttributeName()
    {
        return (isset($this->relationColumnName) == true) ? $this->relationColumnName : null;
    }

    /**
     * Morphed model
     *
     * @return void
     */
    public function related()
    {
        return $this->morphTo('relation');
    }

    /**
     * Relations
     *
     * @return void
     */
    public function relations()
    {    
        return $this->morphToMany($this->getRelationModelClass(),'relation');
    }

    /**
     * Get relations
     *
     * @param integer $id
     * @param string|null $type
     * @return Builder
     */
    public function getRows($id, $type = null) 
    {
        $relationField = $this->getRelationAttributeName();
        $query = (empty($id) == false) ? $this->where($relationField,'=',$id) : $this;

        if (empty($type) == false) {
            $query = $query->where('relation_type','=',$type);
        }

        return $query;
    }

    /**
     * Get relations query for model id
     *
     * @param integer $relation_id
     * @param string|null $type
     * @return Builder
     */
    public function getRelationsQuery($relationId, $type = null) 
    {      
        $query = $this->where('relation_id','=',$relationId);
        if (empty($type) == false) {
            $query = $query->where('relation_type','=',$type);
        }

        return $query;
    }

    /**
     *  Delete relation
     *
     * @param integer|string|null $id
     * @return boolean
     */
    public function deleteRelation($id)
    {
        $model = (empty($id) == true) ? $this->findByid($id) : $this;

        return (is_obejct($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete relations
     *
     * @param integer $id
     * @param string|null $type
     * @param integer|null $relationId
     * @return void
     */
    public function deleteRelations($id, $type = null, $relationId = null)
    {
        $relationField = $this->getRelationAttributeName();
        $model = $this->where($relationField,'=',$id);

        if (empty($type) == false) {
            $model = $model->where('relation_type','=',$type);
        }
        if (empty($relationId) == false) {
            $model = $model->where('relation_id','=',$relationId);
        }
    
        return $model->delete();
    }

    /**
     * Save relation
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return void
     */
    public function saveRelation($id, $type, $relationId)
    {
        $relationField = $this->getRelationAttributeName();
        $data = [
            $relationField  => $id,
            'relation_id'   => $relationId,
            'relation_type' => "$type",
        ];    

        return ($this->hasRelation($id,$type,$relationId) == false) ? $this->create($data) : false;       
    }

    /**
     * Return true if relation exist
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return boolean
     */
    public function hasRelation($id, $type, $relationId)
    {
        $relationField = $this->getRelationAttributeName();
        $model = $this
            ->where($relationField,'=',$id)
            ->where('relation_type','=',$type)
            ->where('relation_id','=',$relationId)->first();
        
        return is_object($model);
    }

    /**
     * Save relations
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return array
     */
    public function saveRelations(array $items, $type, $relationId)
    {
        $added = [];
        foreach ($items as $item) {
            $result = $this->saveRelation($item,$type,$relationId);
            if ($result !== false) {
               $added[] = $item;
            }
        }

        return $added;
    }
}
