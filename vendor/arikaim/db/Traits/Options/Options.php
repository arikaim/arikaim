<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Options;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Uuid;

/**
 * Options table trait
*/
trait Options 
{  
    /**
     * Get option type model class
     *
     * @return string|null
     */
    public function getOptionTypeClass()
    {
        return (isset($this->optionTypeClass) == true) ? $this->optionTypeClass : null;
    }
    
    /**
     * Get optins definition model class
     *
     * @return string|null
     */
    public function getOptionsDefinitionClass()
    {
        return (isset($this->optionsDefinitionClass) == true) ? $this->optionsDefinitionClass : null;
    }

    /**
     * Mutator (get) for value attribute.
     *
     * @return mixed
     */
    public function getValAttribute()
    {
        return (isset($this->attributes['value']) == true) ? $this->attributes['value'] : null;
    }

    /**
     * Option type relation
     *
     * @return mixed
     */
    public function type()
    {
        $optionTypeClass = $this->getOptionTypeClass();
        if (empty($optionTypeClass) == true) {
            return false;
        }

        return $this->belongsTo($optionTypeClass,'type_id');
    }

    /**
     * Create option
     *
     * @param integer|null $referenceId
     * @param string|integer $key Option type key or id
     * @param mixed $value
     * @return Model|false
     */
    public function createOption($referenceId, $key, $value = null)
    {
        if ($this->hasOption($key,$referenceId) == true) {     
            return false;
        }

        $optionType = $this->getOptionType($key);
        if (is_object($optionType) == false) {           
            return false;
        }

        return $this->create([
            'reference_id' => $referenceId,
            'uuid'         => Uuid::create(),
            'type_id'      => $optionType->id,
            'key'          => $optionType->key,
            'value'        => ($value == null) ? $optionType->default : $value,        
        ]);      
    }

    /**
     * Create options
     *
     * @param integer $referenceId
     * @param string $typeName
     * @param string|null $branch
     * @return boolean
     */
    public function createOptions($referenceId, $typeName, $branch = null)
    {
        $optionsList = Model::create($this->getOptionsDefinitionClass());
        if (is_object($optionsList) == false) {
            return false;
        }

        $list = $optionsList->getItems($typeName,$branch);
        foreach ($list as $item) {                  
            $this->createOption($referenceId,$item->key);          
        }

        return true;
    }

    /**
     * Get option type
     *
     * @param string|integer $key Type key or id
     * @return Model|false
     */
    public function getOptionType($key)
    {
        $optionTypeClass = $this->getOptionTypeClass();
        if (empty($optionTypeClass) == true) {
            return false;
        }

        $optionType = Model::create($optionTypeClass);
        if (is_object($optionType) == false) {           
            return false;
        }

        $optionType = (is_numeric($key) == false) ? $optionType->where('key','=',$key) : $optionType->where('id','=',$key);

        return $optionType->first();
    }

    /**
     * Get option
     *
     * @param integer|null $referenceId
     * @param string|integer $key Option typekey or id     
     * @return Model|false
     */
    public function getOption($key, $referenceId = null) 
    {
        $optionType = $this->getOptionType($key);
        if (is_object($optionType) == false) {
            return false;
        }
        $referenceId = (empty($referenceId) == true) ? $this->reference_id : $referenceId;
        $model = $this->where('reference_id','=',$referenceId);

        $model = (is_numeric($key) == true) ? $model->where('type_id','=',$key) : $model->where('key','=',$key);
        
        return $model->first();                    
    }

    /**
     * Get options query
     *
     * @param integer $referenceId
     * @return QueryBuilder
     */
    public function getOptionsQuery($referenceId)
    {
        return $this->where('reference_id','=',$referenceId);
    }

    /**
     * Get options list
     *
     * @param integer $referenceId
     * @return Model|null
     */
    public function getOptions($referenceId)
    {
        return $this->getOptionsQuery($referenceId)->get();
    }

    /**
     * Return true if option name exist
     *
     * @param integer|null $referenceId
     * @param string $key
     * @return boolean
     */
    public function hasOption($key, $referenceId = null)
    {
        $model = $this->getOption($key,$referenceId);

        return is_object($model);
    }

    /**
     * Save option
     *
     * @param integer $referenceId
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function saveOption($referenceId, $key, $value) 
    {
        if ($this->hasOption($key,$referenceId) == false) {          
            return $this->createOption($referenceId,$key,$value);
        }

        $optionType = $this->getOptionType($key);
        if (is_object($optionType) == false) {
            return false;
        }

        $model = $this->where('reference_id','=',$referenceId)->where('type_id','=',$optionType->id);

        return $model->update(['value' => $value]);  
    }

    /**
     * Save options
     *
     * @param integer $referenceId
     * @param array $data
     * @return boolean
     */
    public function saveOptions($referenceId, array $data)
    {
        $errors = 0;
        foreach ($data as $key => $value) {
            $result = $this->saveOption($referenceId,$key,$value);
            $errors += ($result !== false) ? 0 : 1; 
        }      
        
        return ($errors == 0);
    }
}
