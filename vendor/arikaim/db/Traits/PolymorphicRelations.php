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

use Arikaim\Core\Utils\Uuid;

/**
 *  Polymorphic Relations (Many To Many) trait      
*/
trait PolymorphicRelations 
{           
    /**
     * Get relation model class
     *
     * @return string|null
     */
    public function getRelationModelClass(): ?string
    {
        return $this->relationModelClass ?? null;
    }

    /**
     * Get relation attribute name
     *
     * @return string|null
     */
    public function getRelationAttributeName(): ?string
    {
        return $this->relationColumnName ?? null;
    }

    /**
     * Morphed model
     * 
     * @param string|null $type
     * @return mixed
     */
    public function related(?string $type = null)
    {
        return $this->morphTo('relation',$type);      
    }

    /**
     * Relations
     *
     * @return Relation
     */
    public function relations()
    {    
        return $this->morphToMany($this->getRelationModelClass(),'relation');
    }

    /**
     * Get relations
     *
     * @param integer|null $id
     * @param string|null $type
     * @return Builder
     */
    public function getItemsQuery(?int $id, ?string $type = null): object 
    {
        $query = (empty($id) == false) ? $this->where($this->getRelationAttributeName(),'=',$id) : $this;

        return (empty($type) == false) ? $query->where('relation_type','=',$type) : $query;
    }

    /**
     * Return true if related items > 0 
     *
     * @param integer|null $id
     * @param string|null $type
     * @return boolean
     */
    public function hasRelatedItems(?int $id, ?string $type = null): bool
    {
        return ($this->getItemsQuery($id,$type)->count() > 0);
    }

    /**
     * Get relations items
     *
     * @param integer|null $relationId
     * @param string|null $type
     * @return Collection|null
     */
    public function getRelatedItems(?int $relationId, ?string $type = null)
    {
        $relationField = $this->getRelationAttributeName();
     
        return $this
            ->getRelationsQuery($relationId,$type)
            ->get($relationField)
            ->pluck($relationField);
    }

    /**
     * Get relations query for model id
     *
     * @param integer|null $relation_id
     * @param string|null $type
     * @return Builder
     */
    public function getRelationsQuery(?int $relationId, ?string $type = null): object 
    {      
        $query = $this->where('relation_id','=',$relationId);
        
        return (empty($type) == false) ? $query->where('relation_type','=',$type) : $query;
    }

    /**
     *  Delete relation
     *
     * @param integer|string|null $id
     * @return boolean
     */
    public function deleteRelation($id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);

        return ($model == null) ? false : (bool)$model->delete();
    }

    /**
     * Delete relations
     *
     * @param integer|null $id
     * @param string|null $type
     * @param integer|null $relationId
     * @return boolean
     */
    public function deleteRelations(?int $id, ?string $type = null, ?int $relationId = null): bool
    {
        $relationField = $this->getRelationAttributeName();
        $model = $this->where($relationField,'=',$id);
        
        if (empty($type) == false) {
            $model = $model->where('relation_type','=',$type);
        }
        if (empty($relationId) == false) {
            $model = $model->where('relation_id','=',$relationId);
        }
               
        return (bool)$model->delete();
    }

    /**
     * Save relation
     *
     * @param integer|null $id
     * @param string|null  $type
     * @param integer|null $relationId
     * @return Model|false
     */
    public function saveRelation(?int $id, ?string $type, ?int $relationId)
    {
        if (empty($relationId) == true || empty($id) == true) {
            return false;
        }
      
        $data = [           
            $this->getRelationAttributeName()  => $id,
            'relation_id'                      => $relationId,
            'relation_type'                    => $type,
        ];    
    
        $model = $this->getRelationModel($id,$type,$relationId);
        if ($model === false) {
            $data['uuid'] = Uuid::create();
            return $this->create($data);
        }

        $result = $model->update($data);

        return ($result === false) ? false : $model;
    }

    /**
     * Return true if relation exist
     *
     * @param integer|null $id
     * @param string|null  $type
     * @param integer|null $relationId
     * @return boolean
     */
    public function hasRelation(?int $id, ?string $type, ?int $relationId): bool
    {
        $model = $this
            ->where($this->getRelationAttributeName(),'=',$id)
            ->where('relation_type','=',$type)
            ->where('relation_id','=',$relationId)->first();
        
        return ($model != null);
    }

    /**
     * Get relation
     *
     * @param integer|null $id
     * @param string|null  $type
     * @param integer|null $relationId
     * @return Model|false
     */
    public function getRelationModel(?int $id, ?string $type, ?int $relationId)
    {
        $model = $this
            ->where($this->getRelationAttributeName(),'=',$id)
            ->where('relation_type','=',$type)
            ->where('relation_id','=',$relationId)->first();
        
        return ($model != null) ? $model : false;
    }

    /**
     * Save relations
     *
     * @param array $items
     * @param string|null  $type
     * @param integer|null $relationId
     * @return array
     */
    public function saveRelations(array $items, ?string $type, ?int $relationId): array
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
