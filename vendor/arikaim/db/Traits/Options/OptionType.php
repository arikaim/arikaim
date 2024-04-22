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

use Arikaim\Core\Collection\Property;

/**
 * Option type table trait
*/
trait OptionType 
{    
    /**
     * Get option type id
     *
     * @param string $type
     * @return integer|null
     */
    public static function getOptionTypeId($type)
    {
        $result = \array_search($type,Property::TYPES);

        return ($result == false) ? 0 : $result;
    }

    /**
     * Get option type name
     *
     * @param int|null $type
     * @return string|false
     */
    public function getTypeText(?int $type = null)
    {
        $type = $type ?? $this->type;

        return Property::TYPES[$type] ?? false;
    }

    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootOptionType()
    {
        $fillable = [
            'key',
            'title',
            'description',
            'hidden',
            'readonly',
            'default',
            'items',
            'items_type',
            'data_source',
            'data_source_type'      
        ];

        static::retrieved(function($model) use ($fillable) {
            $model->fillable = \array_merge($model->fillable,$fillable);
        });

        static::saving(function($model) use ($fillable) {
            $model->fillable = \array_merge($model->fillable,$fillable);
        });
    }

    /**
     * Time interval option type
     *
     * @return integer
     */
    public function TIME_INTERVAL(): int
    {
        return Property::getTypeIndex('time-interval');
    }

    /**
     * Date time option type
     *
     * @return integer
     */
    public function DATE(): int
    {
        return Property::getTypeIndex('date');
    }

    /**
     * Text type option
     *
     * @return integer
     */
    public function TEXT(): int
    {
        return Property::getTypeIndex('text');
    }

    /**
     * Text area type option
     *
     * @return integer
     */
    public function TEXTAREA()
    {
        return Property::getTypeIndex('text-area');
    }

    /**
     * Number type option
     *
     * @return integer
     */
    public function NUMBER()
    {
        return Property::getTypeIndex('number');
    }

    /**
     * Price type option
     *
     * @return integer
     */
    public function PRICE()
    {
        return Property::getTypeIndex('price');
    }
    
    /**
     * Mutator (set) for items attribute.
     *
     * @param array $value
     * @return void
     */
    public function setItemsAttribute($value)
    {
        $value = (\is_array($value) == true) ? $value : [$value];    
        $this->attributes['items'] = \json_encode($value);
    }

    /**
     * Mutator (get) for items attribute.
     *
     * @return array
     */
    public function getItemsAttribute()
    {
        return (empty($this->attributes['items']) == true) ? [] : \json_decode($this->attributes['items'],true);
    }

    /**
     * Get option type
     *
     * @param string $key
     * @return mixed
     */
    public function getByKey(string $key)
    {
        return $this->findByColumn($key,'key');
    }
}
