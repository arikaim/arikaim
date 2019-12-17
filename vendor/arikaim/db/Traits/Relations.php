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
     * @return string
     */
    public function getRelationsTargetAttributeName()
    {
        return (isset($this->relationTargetColumn) == true) ? $this->relationTargetColumn : null;
    }

    /**
     * Get relations source refernce column name 
     *
     * @return string
     */
    public function getRelationsSourceAttributeName()
    {
        return (isset($this->relationSourceColumn) == true) ? $this->relationSourceColumn : null;
    }

    /**
     * Get single relation model
     *
     * @param integer $id
     * @return Model|false
     */
    public function relation($id)
    {
        $targetColumn= $this->getRelationsTargetAttributeName();
        $model = $this->getQuery()->where($targetColumn,'=',$id)->first();  

        return (is_object($model) == false) ? false : $model;
    }

    /**
     * Add relation
     *
     * @param integer $targetId
     * @param array $data
     * @return Model|false
     */
    public function addRelation($targetId, $data = [])
    {        
        $model = $this->relation($targetId);

        if (is_object($model) == false) {
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
    public function addRelations(array $items)
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
    public function hasRelation($targetId)
    {
        return is_object($this->relation($targetId));
    }

    /**
     * Delete translation
     *
     * @param integer $id
     * @param string $language
     * @return boolean
     */
    public function removeRelation($id)
    {        
        $model = $this->relation($id);
        
        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete all relations
     *
     * @return boolean
     */
    public function removeRelations()
    {
        return $this->delete();      
    }
}
