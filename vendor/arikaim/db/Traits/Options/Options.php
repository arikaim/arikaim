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
    public function getOptionTypeClass(): ?string
    {
        return $this->optionTypeClass ?? null;
    }
    
    /**
     * Mutator (get) for value attribute.
     *
     * @return mixed
     */
    public function getValAttribute()
    {
        return $this->attributes['value'] ?? null;
    }

    /**
     * Option type relation
     *
     * @return Relation|null
     */
    public function type()
    {
        return $this->belongsTo($this->getOptionTypeClass(),'type_id');
    }

    /**
     * Create option
     *
     * @param integer|null $referenceId
     * @param string $key Option type key
     * @param mixed $value
     * @return Model|null
     */
    public function createOption(?int $referenceId, string $key, $value = null): ?object
    {
        if ($this->hasOption($key,$referenceId) == true) {     
            return null;
        }

        $optionType = $this->getOptionType($key);
        
        return $this->create([
            'reference_id' => $referenceId,
            'uuid'         => Uuid::create(),
            'type_id'      => ($optionType == null) ? null : $optionType->id,
            'key'          => $key,
            'value'        => ($value == null && $optionType != null) ? $optionType->default : $value,        
        ]);      
    }

    /**
     * Get option type
     *
     * @param string $key Type key
     * @return Model|null
     */
    public function getOptionType(string $key): ?object
    {
        return $this->type()->where('key','=',$key)->first();
    }

    /**
     * Get option
     *
     * @param string $key
     * @param integer|null $referenceId
     * @param string $key Option type key
     * @return Model|null
     */
    public function getOption(string $key, ?int $referenceId = null): ?object
    {
        $referenceId = (empty($referenceId) == true) ? $this->reference_id : $referenceId;
       
        return $this
                    ->where('reference_id','=',$referenceId)
                    ->where('key','=',$key)->first();                    
    }

    /**
     * Get option value
     *
     * @param string  $key
     * @param int|null $referenceId
     * @param mixed $default
     * @return mixed
     */
    public function getOptionValue($key, ?int $referenceId = null, $default = null) 
    {
        $model = $this->getOption($key,$referenceId);

        return ($model == null) ? $default : $model->value; 
    }

    /**
     * Get options query
     *
     * @param integer|null $referenceId
     * @param array|null $keys
     * @return QueryBuilder
     */
    public function getOptionsQuery(?int $referenceId, array $keys = null)
    {
        $query = $this->where('reference_id','=',$referenceId);
        
        return (empty($keys) == false) ? $query->whereIn('key',$keys) : $query;          
    }

    /**
     * Get options list
     *
     * @param integer|null $referenceId
     * @param array|null $keys
     * @return Model|null
     */
    public function getOptions(?int $referenceId, ?array $keys = null)
    {
        return $this->getOptionsQuery($referenceId,$keys)->get();
    }

    /**
     * Return true if option name exist
     *
     * @param integer|null $referenceId
     * @param string $key
     * @return boolean
     */
    public function hasOption(string $key, ?int $referenceId = null): bool
    {      
        return ($this->getOption($key,$referenceId) !== null);
    }

    /**
     * Save option
     *
     * @param integer $referenceId
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function saveOption(int $referenceId, string $key, $value) 
    {
        if ($this->hasOption($key,$referenceId) == false) {          
            return $this->createOption($referenceId,$key,$value);
        }

        return $this
            ->where('reference_id','=',$referenceId)
            ->where('key','=',$key)
            ->update([
                'value' => $value
            ]);       
    }

    /**
     * Save options
     *
     * @param integer $referenceId
     * @param array $data
     * @return boolean
     */
    public function saveOptions(int $referenceId, array $data)
    {
        $errors = 0;
        foreach ($data as $key => $value) {
            $result = $this->saveOption($referenceId,$key,$value);
            $errors += ($result !== false) ? 0 : 1; 
        }      
        
        return ($errors == 0);
    }
}
