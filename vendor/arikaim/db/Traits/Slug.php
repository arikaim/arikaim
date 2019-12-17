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

use Arikaim\Core\Utils\Utils;

/**
 * Create slug
*/
trait Slug 
{    
    /**
     * Set model event on saving
     *
     * @return void
     */
    public static function bootSlug()
    {
        static::saving(function($model) {   
            $model = Self::saveSlug($model);
        });        
    }

    /**
     * Get slug attribute name
     *
     * @return string
     */
    public function getSlugColumn()
    {
        return (isset($this->slugColumn) == true) ? $this->slugColumn : 'slug';
    }

    /**
     * Get slug source attribute name
     *
     * @return string
     */
    public function getSlugSourceColumn()
    {
        return (isset($this->slugSourceColumn) == true) ? $this->slugSourceColumn : 'title';
    }

    /**
     * Get slug separator
     *
     * @return void
     */
    public function getSlugSeparator()
    {
        return (isset($this->slugSeparator) == true) ? $this->slugSeparator : '-';
    }

    /**
     * Save slug
     *
     * @param string $text
     * @param string $options
     * @return string
     */
    public static function saveSlug($model)
    {
        $slugColumn = $model->getSlugColumn();
        $slugSourceColumn = $model->getSlugSourceColumn();
        $separator = $model->getSlugSeparator(); 

        if (is_null($model->$slugSourceColumn) == false) {                   
            $model->attributes[$slugColumn] = Utils::slug($model->$slugSourceColumn,$separator);
        }              
       
        return $model;
    }

    /**
     * Create slug from text
     *
     * @param string $text
     * @return string
     */
    public function slug($text)
    {
        return Utils::slug($text,$this->getSlugSeparator());
    }

    /**
     * Find model by slug
     *
     * @param string $slug
     * @return Model
     */
    public function findBySlug($slug)
    {
        $slugColumn = $this->getSlugColumn();
        $model = $this->where($slugColumn,'=',$slug)->first();

        return (is_object($model) == true) ? $model : false;
    }
}
