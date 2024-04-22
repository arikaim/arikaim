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

use Arikaim\Core\Utils\Uuid as UuidFactory;

/**
 * 
 * Update UUID field
 *     
*/
trait Uuid 
{    
    /**
     * Default uuid column name
     *
     * @var string
     */
    protected static $DEFAULT_UUID_COLUMN = 'uuid';

    /**
     * Init model events.
     *
     * @return void
     */
    public static function bootUuid()
    {
        static::retrieved(function($model) {
            if (isset($model->fillable[$model->uuidColumnName ?? static::$DEFAULT_UUID_COLUMN]) == false) {               
                $model->fillable[] = $model->uuidColumnName ?? static::$DEFAULT_UUID_COLUMN;
            }             
        });

        static::creating(function($model) {   
            if (empty($model->{$model->uuidColumnName ?? static::$DEFAULT_UUID_COLUMN}) == true) {        
                $model->attributes[$model->uuidColumnName ?? static::$DEFAULT_UUID_COLUMN] = UuidFactory::create();
            }
        });
    }
}
