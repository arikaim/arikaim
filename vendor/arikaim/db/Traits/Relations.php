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
 *  Relations (Many To Many) trait      
*/
trait Relations 
{           
    /**
     * Get relations target refernce column name 
     *
     * @return string|null
     */
    public function getRelationsTargetAttributeName(): ?string
    {
        return $this->relationTargetColumn ?? null;
    }

    /**
     * Get relations source refernce column name 
     *
     * @return string|null
     */
    public function getRelationsSourceAttributeName(): ?string
    {
        return $this->relationSourceColumn ?? null;
    }

    /**
     * Get single relation model
     *
     * @param integer $id
     * @return Model|false
     */
    public function relation($id)
    {
        $model = $this->getQuery()->where($this->getRelationsTargetAttributeName(),'=',$id)->first();  

        return ($model == null) ? false : $model;
    }

    /**
     * Add relation
     *
     * @param integer $targetId
     * @param array $data
     * @return Model|false
     */
    public function addRelation($targetId, array $data = [])
    {        
        $model = $this->relation($targetId);

        if ($model === false) {
            $targetColumn = $this->getRelationsTargetAttributeName();
            $sourceColumn = $this->getRelationsSourceAttributeName();

            $data[$targetColumn] = $targetId;     
            $data[$sourceColumn] = $this->$sourceColumn;

            return $this->create($data);
        }
    
        return false;
    }

    /**
     * Add relations 
     *
     * @param array $items
     * @return void
     */
    public function addRelations(array $items): void
    {        
        foreach ($items as $key => $id) {
            $this->addRelation($id);
        }   
    }

    /**
     * Return true if relation to target id extist
     *
     * @param integer $target_id
     * @return boolean
     */
    public function hasRelation($targetId): bool
    {
        return ($this->relation($targetId) !== false);
    }

    /**
     * Delete translation
     *
     * @param integer $id
     * @return boolean
     */
    public function removeRelation($id): bool
    {        
        $model = $this->relation($id);
        
        return ($model !== false) ? (bool)$model->delete() : false;
    }

    /**
     * Delete all relations
     *
     * @return boolean
     */
    public function removeRelations(): bool
    {
        return (bool)$this->delete();      
    }
}
