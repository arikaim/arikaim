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

/**
 * Options relation table trait
*/
trait OptionsRelation 
{   
    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootOptionsRelation()
    {
        static::created(function($model) {      
            $model->createOptions(); 
        });
    }

    /**
     * Get options type name
     *
     * @return string|null
     */
    public function getOptionsType()
    {
        return null;
    }

    /**
     * Create options
     *
     * @return boolean
     */
    public function createOptions()
    {
        $options = Model::create($this->getOptionsClass());
        $typeName = $this->getOptionsType();
        
        if (is_object($options) == true && empty($typeName) == false) {
            return $options->createOptions($this->id,$typeName);
        }

        return false;
    } 

    /**
     * Get option model class
     *
     * @return string|null
     */
    public function getOptionsClass()
    {
        return (isset($this->optionsClass) == true) ? $this->optionsClass : null;
    }

    /**
     * Options relation
     *
     * @return mixed
     */
    public function options()
    {
        $relation = $this->hasMany($this->getOptionsClass(),'reference_id');

        return $relation;
    }

    /**
     * Create options_list attribute used for better collection serialization key => value 
     *
     * @return Collection
     */
    public function getOptionsListAttribute()
    {
        $options = $this->options()->get()->keyBy('key')->map(function ($item, $key) {
            return $item['value'];
        });

        return $options;
    }

    /**
     * Get option
     *
     * @param string $key
     * @return mixed|null
     */
    public function getOption($key)
    {
        if (is_object($this->options) == false) {
            return null;
        }
        
        $items = $this->options->keyBy('key');

        return (is_object($items) == true) ? $items->get($key) : null;   
    }
}
