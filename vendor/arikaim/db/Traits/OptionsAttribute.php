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
 * Options attribute trait
*/
trait OptionsAttribute 
{        
    /**
     * Mutator (set) for options attribute.
     *
     * @param array $value
     * @return void
     */
    public function setOptionsAttribute($value)
    {
        $value = (is_array($value) == true) ? $value : [$value];    
        $this->attributes['options'] = json_encode($value);
    }

    /**
     * Mutator (get) for options attribute.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        return (empty($this->attributes['options']) == true) ? [] : json_decode($this->attributes['options'],true);
    }

    /**
     * Get option from options array
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return (isset($this->options[$key]) == true) ? $this->options[$key] : $default;
    }
}
