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
 * Options table trait
*/
trait Options 
{    
    /**
     * Option type constant 
     */
    static $TEXT        = 0;
    static $CHECKBOX    = 1;
    static $DROPDOWN    = 2;
    static $TEXT_AREA   = 3;
    static $RELATION    = 4;

    /**
     * Text type option
     *
     * @return integer
     */
    public function TEXT()
    {
        return Self::$TEXT;
    }

    /**
     * Checkbox type option
     *
     * @return integer
     */
    public function CHECKBOX()
    {
        return Self::$CHECKBOX;
    }

    /**
     * Dropdown type option
     *
     * @return integer
     */
    public function DROPDOWN()
    {
        return Self::$DROPDOWN;
    }

    /**
     * Text area type option
     *
     * @return integer
     */
    public function TEXT_AREA()
    {
        return Self::$TEXT_AREA;
    }

    /**
     * Relation type option
     *
     * @return integer
     */
    public function RELATION()
    {
        return Self::$RELATION;
    }

    /**
     * Mutator (get) for value attribute.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        return (empty($this->attributes['value']) == true) ? $this->attributes['default'] : $this->attributes['value'];
    }

    /**
     * Mutator (set) for items attribute.
     *
     * @param array $value
     * @return void
     */
    public function setItemsAttribute($value)
    {
        $value = (is_array($value) == true) ? $value : [$value];    
        $this->attributes['items'] = json_encode($value);
    }

    /**
     * Mutator (get) for items attribute.
     *
     * @return array
     */
    public function getItemsAttribute()
    {
        return (empty($this->attributes['items']) == true) ? [] : json_decode($this->attributes['items'],true);
    }

    /**
     * Read option
     *
     * @param integer $referenceId
     * @param string $key     
     * @param mixed $default
     * @return mixed
     */
    public function read($referenceId, $key, $default = null) 
    {
        $model = $this->findOption($referenceId,$key);
        return (is_object($model) == false) ? $default : $model->value;                      
    }

    /**
     * Get options
     *
     * @param integer $id
     * @return QueryBuilder
     */
    public function getOptions($id)
    {
        return $this->where('reference_id','=',$id);
    }

    /**
     * Return true if option name exist
     *
     * @param integer $referenceId
     * @param string $key
     * @return boolean
     */
    public function hasOption($referenceId, $key)
    {
        $model = $this->findOption($referenceId,$key);

        return is_object($model);
    }

    /**
     * Fidn option
     *
     * @param integer $referenceId
     * @param string $key
     * @return Model|null
     */
    public function findOption($referenceId, $key)
    {
        return $this->where('reference_id','=',$referenceId)->where('key','=',$key)->first();
    }
 
    /**
     * Save option
     *
     * @param integer $referenceId
     * @param string $key
     * @param mixed $value
     * @return Model|bool
     */
    public function set($referenceId, $key, $value) 
    {
        $key = trim($key);
        if (empty($key) == true) {
            return false;
        }
    
        if (is_array($value) == true) {            
            $value = Utils::jsonEncode($value,true);           
        }

        $data = [
            'reference_id' => $referenceId,
            'key'          => $key,
            'value'        => $value
        ];      
        $model = $this->findOption($referenceId,$key);  
        $result = (is_object($model) == true) ? $model->update($data) : $this->create($data);             
    
        return $result;
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
            $result = $this->set($referenceId,$key,$value);
            $errors += ($result !== false) ? 0 : 1; 
        }      
        
        return ($errors == 0);
    }

    /**
     * Create option, if option exists return false
     *
     * @param integer $referenceId
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function createOption($referenceId, $key, $value)
    {
        return ($this->hasOption($referenceId,$key) == true) ? false : $this->set($referenceId,$key,$value);       
    }
}
