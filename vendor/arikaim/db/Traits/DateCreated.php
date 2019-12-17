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

use Arikaim\Core\Utils\DateTime;

/**
 * Set current time for date created.
 * 
 * Change default date created attribute in model class 
 *      protected $dateCreatedColumn = 'db column name';
*/
trait DateCreated
{    
    /**
     * Set model events
     *
     * @return void
     */
    public static function bootDateCreated()
    {
        static::creating(function($model) {  
            $name = $model->getDateCreatedAttributeName();   
            $model->attributes[$name] = DateTime::getTimestamp();                   
        });
    }
    
    /**
     *  Get date created attribute
     *
     * @return string
     */
    public function getDateCreatedAttributeName()
    {
        return (isset($this->dateCreatedColumn) == true) ? $this->dateCreatedColumn : 'date_created';
    }
}
