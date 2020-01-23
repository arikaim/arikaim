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

/**
 * Option type table trait
*/
trait OptionType 
{    
    /**
     * Option type constant 
     */
    static $TEXT          = 0;
    static $CHECKBOX      = 1;
    static $DROPDOWN      = 2;
    static $TEXT_AREA     = 3;
    static $RELATION      = 4;
    static $NUMBER        = 5;
    static $IMAGE         = 6;
    static $PRICE         = 7;
    static $FILE          = 8;
    static $MARKDOWN      = 9;
    static $DATE          = 10;
    static $TIME_INTERVAL = 11;

    /**
     *  Option type text
     */
    static $TYPES_LIST = [
        'text',
        'checkbox',
        'dropdown',
        'textarea',
        'relation',
        'number',
        'image',
        'price',
        'file',
        'markdown',
        'date',
        'time-interval'
    ];

    /**
     * Get option type id
     *
     * @param string $type
     * @return integer|null
     */
    public static function getOptionTypeId($type)
    {
        return array_search($type,Self::$TYPES_LIST);
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
            'items_type'      
        ];

        static::retrieved(function($model) use ($fillable) {
            $model->fillable = array_merge($model->fillable,$fillable);
        });

        static::saving(function($model) use ($fillable) {
            $model->fillable = array_merge($model->fillable,$fillable);
        });
    }

    /**
     * Time interval option type
     *
     * @return integer
     */
    public function TIME_INTERVAL()
    {
        return Self::$TIME_INTERVAL;
    }

    /**
     * Date time option type
     *
     * @return integer
     */
    public function DATE()
    {
        return Self::$DATE;
    }

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
    public function TEXTAREA()
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
     * Number type option
     *
     * @return integer
     */
    public function NUMBER()
    {
        return Self::$NUMBER;
    }

    /**
     * Price type option
     *
     * @return integer
     */
    public function PRICE()
    {
        return Self::$PRICE;
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
     * Get option type
     *
     * @param string $key
     * @return mixed
     */
    public function getByKey($key)
    {
        return $this->findByColumn($key,'key');
    }
}
